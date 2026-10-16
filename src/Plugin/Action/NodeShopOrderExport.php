<?php

namespace Drupal\nodeshop_export\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Form\EnforcedResponseException;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\nodeshop\Entity\Order;
use Drupal\nodeshop\Entity\OrderProduct;
use Drupal\nodeshop\NodeShop;
use Drupal\nodeshop_export\Export\Helper;
use Drupal\nodeshop_export\Export\Export;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Exports orders to be imported into the "Verlags-DB".
 *
 * @Action(
 *   id = "nodeshop_export_export",
 *   label = @Translation("Exports the selected orders"),
 *   type = "nodeshop_order"
 * )
 */
class NodeShopOrderExport extends ActionBase
{
    use LoggerChannelTrait;
    use MessengerTrait;
    use StringTranslationTrait;

    /**
     * Holds the export data
     *
     * @var array
     */
    private $export_data = [];

    /**
     * Problems found while exporting, e.g. products of deleted nodes.
     *
     * @var string[]
     */
    private $warnings = [];

    /**
     * {@inheritdoc}
     *
     * @param \Drupal\nodeshop\Entity\Order|null $object
     *   The order to export.
     */
    public function execute($object = null)
    {
        if (!$object instanceof Order) {
            return;
        }
        $order = $object;
        foreach ($order->getOrderProducts() as $order_product) {
            //Means OrderArray - not elegant but usefull
            $node = $order_product->get('nid')->entity;
            // the product of a deleted node can not be exported, but the other products of the order can
            if (!$node instanceof NodeInterface) {
                $this->warnings[] = 'Bestellung ' . $order->id() . ': ein Produkt gehört zu einem gelöschten Inhalt '
                    . 'und wurde nicht exportiert.';
                continue;
            }
            // $oa = array_merge($row, $entry, array('order'=>$o, 'node' => $node));
            $export = [];
            // only used to name the order in warnings, it is not part of the CSV
            $export['_order'] = $order->id();
            $export['anrede']           = Helper::getSalutation(Helper::getValue($order, 'customer_title'));
            $export['titel']            = Helper::getTitle(Helper::getValue($order, 'customer_title'));
            $export['familienname']     = Helper::getValue($order, 'customer_last_name');
            $export['vorname']          = Helper::getValue($order, 'customer_first_name');

            $export['land']             = Helper::getCountryCode(Helper::getValue($order, 'customer_country'));
            $export['plz_strasse']      = Helper::getValue($order, 'customer_postal_code');
            $export['strasse']          = Helper::getValue($order, 'customer_street');
            $export['ort']              = Helper::getValue($order, 'customer_city');
            $export['adressmerkmal1']   = Helper::getValue($order, 'customer_street_addon');
            $export['email']            = $order->getCustomerEmail();
            $export['rechnungsart']     = Helper::getPaymentLabel($this->t($order->getPaymentMethodLabel())->__toString());
            //Abweichende Lieferadresse, falls
            $export['lanrede']          = Helper::getSalutation(Helper::getValue($order, 'delivery_title'));
            $export['ltitel']           = Helper::getTitle(Helper::getValue($order, 'delivery_title'));
            $export['lfamilienname']    = Helper::getValue($order, 'delivery_last_name');
            $export['lvorname']         = Helper::getValue($order, 'delivery_first_name');

            $export['lland']            = Helper::getCountryCode(Helper::getValue($order, 'delivery_country'));
            $export['lplz_strasse']     = Helper::getValue($order, 'delivery_postal_code');
            $export['lstrasse']         = Helper::getValue($order, 'delivery_street');
            $export['lstrasse']        .= ' '.Helper::getValue($order, 'delivery_street_number');
            $export['lort']             = Helper::getValue($order, 'delivery_city');
            $export['lfirma_1']         = Helper::getValue($order, 'delivery_street_addon');
            // //Bestellungen buecher, onlineartikel etc...
            if ((
                !$node->hasField('field_warenid')
                || $node->hasField('field_warenid')
                && empty($node->get('field_warenid')->value)
                )
                || (
                    $node->hasField('field_warenid')
                    && "1" != Helper::getValue($node, 'field_warenid')
                    && "2" != Helper::getValue($node, 'field_warenid')
                    && "5" != Helper::getValue($node, 'field_warenid')
                )
            ) {
                if ($node->hasField('field_warenid') && !empty(Helper::getValue($node, 'field_warenid'))) {
                    //feste Artikelnummer
                    $export['bartikelnummer'] = Helper::getValue($node, 'field_warenid');
                } elseif ('story' == $node->bundle()) {
                    //globale Artikelnummer für alle Onlineartikel
                      // Preis: 1€: Art1
                      // Preis: 2€: Art2
                      // Preis: 3€: Art3
                    $export['bartikelnummer'] = 'Art'.substr(NodeShop::getPrice($node), 0, 1);
                } elseif ('ausgabe' == $node->bundle()) {
                // Preis 9,50 (neue Ausgaben): apart onl
                    if ('9.50' == NodeShop::getPrice($node)) {
                        $export['bartikelnummer'] = 'apart onl';
                    }
                  // Preis 9,00 (alte Ausgaben): apartonl9
                    if ('9.00' == NodeShop::getPrice($node)) {
                        $export['bartikelnummer'] = 'apartonl9';
                    }
                }
                $export['bmenge'] = $order_product->getQuantity();
                $export['bbestelldatum'] = \Drupal::service('date.formatter')->format($this->getOrderDate($order), 'custom', 'Ymd');
                $export['bversandkosten_fix'] = '000000';
                $export['bversandkostenjn'] = 'n';
            } else {
              //Bestellungen abo
                $export['bzeitschriftnr'] = Helper::getValue($node, 'field_warenid');
                $export['bpreisnummer'] = Helper::getValue($node, 'field_preisnr');
                $export['bbestelldatum'] = \Drupal::service('date.formatter')->format($this->getOrderDate($order), 'custom', 'Ymd');
            }
            // delivery starts with current edition.
            $export['blieferung_ab_ausgabe'] = 'Ja';

            // $order_product_data = $order_product->getData()->getValue()[0];
            // if (isset($order_product_data['bonus']) && !empty($order_product_data['bonus'])) {
            //     // if not an array, the bonus key only contains the node id of the bonus
            //     if (!is_array($order_product_data['bonus'])) {
            //         $bonus_node_id = $order_product_data['bonus'];
            //     } elseif (isset($order_product_data['bonus']['nid'])) {
            //         $bonus_node_id = $order_product_data['bonus']['nid'];
            //     } else {
            //         continue;
            //     }
            //     $bonus= \Drupal::entityTypeManager()->getStorage('node')->load($bonus_node_id);
            //     //Abo-Praemie
            //     if ('' != Helper::getValue($bonus, 'field_warenid')) {
            //         $export['partikelnr'] = Helper::getValue($bonus, 'field_warenid');
            //     }

            //     if ($order->hasDeliveryAddress()) {
            //     //praemienempfaenger an lieferadresse
            //         $export['panrede']          = Helper::getSalutation(Helper::getValue($order, 'delivery_title'));
            //         $export['ptitel']           = Helper::getTitle(Helper::getValue($order, 'delivery_title'));
            //         $export['pfamilienname']    = Helper::getValue($order, 'delivery_last_name');
            //         $export['pvorname']         = Helper::getValue($order, 'delivery_first_name');
            //         ;
            //         $export['pland']            = Helper::getCountryCode(Helper::getValue($order, 'delivery_country'));
            //         $export['pplz_strasse']     = Helper::getValue($order, 'delivery_postal_code');
            //         $export['pstrasse']         = Helper::getValue($order, 'delivery_street');
            //         $export['port']             = Helper::getValue($order, 'delivery_city');
            //         $export['padressmerkmal1']  = Helper::getValue($order, 'delivery_street_addon');
            //         $export['ppraemie'] = 2;
            //     } else {
            //       //praemienempfaenger an rechnungsadresse
            //         $export['panrede']          = Helper::getSalutation(Helper::getValue($order, 'customer_title'));
            //         $export['ptitel']           = Helper::getTitle(Helper::getValue($order, 'customer_title'));
            //         $export['pfamilienname']    = Helper::getValue($order, 'customer_last_name');
            //         $export['pvorname']         = Helper::getValue($order, 'customer_first_name');
            //         ;
            //         $export['pland']            = Helper::getCountryCode(Helper::getValue($order, 'customer_country'));
            //         $export['pplz_strasse']     = Helper::getValue($order, 'customer_postal_code');
            //         $export['pstrasse']         = Helper::getValue($order, 'customer_street');
            //         $export['pstrasse']        .= ' '.Helper::getValue($order, 'customer_street_number');
            //         $export['port']             = Helper::getValue($order, 'customer_city');
            //         $export['padressmerkmal1']  = Helper::getValue($order, 'customer_street_addon');
            //         $export['ppraemie'] = 1;
            //     }
            // }
            $this->export_data[] = $export;
        }
    }

    /**
     * The entry of the order history, when the user submits the checkout.
     *
     * @see \Drupal\nodeshop\Form\OrderCheckoutForm::submitForm()
     */
    const HISTORY_CHECKOUT_FINISHED = 'User finished Checkout.';

    /**
     * The entry of the order history, when the status of the order changes.
     *
     * @see \Drupal\nodeshop\Entity\Order::setOrderStatus()
     */
    const HISTORY_STATUS_CHANGED = 'Set order status from "@old_status" to "@new_status".';

    /**
     * Returns the date of the order.
     *
     * The order is created, when the user starts the checkout in the cart, and may be completed much later. So the
     * date is taken from the order history, in this order:
     * - the first change of the status to completed (the order is placed and, if necessary, paid),
     * - the first submit of the checkout,
     * - the creation of the order, e.g. for orders migrated from the lightshop, that have no history,
     * - the last change of the order.
     * The last change is only a fallback, as it changes with every save, e.g. when the order is marked as exported.
     *
     * @param \Drupal\nodeshop\Entity\Order $order
     * @return int The timestamp of the order
     */
    protected function getOrderDate(Order $order)
    {
        $checkout_finished = null;
        $entries = \Drupal::database()->query(
            'SELECT entry, variables, timestamp FROM {nodeshop_order_history}'
            . ' WHERE order_id = :order_id AND entry IN (:entries[]) ORDER BY timestamp ASC, id ASC',
            [
                ':order_id' => $order->id(),
                ':entries[]' => [self::HISTORY_CHECKOUT_FINISHED, self::HISTORY_STATUS_CHANGED],
            ]
        )->fetchAll();

        foreach ($entries as $entry) {
            if (self::HISTORY_STATUS_CHANGED === $entry->entry) {
                $variables = @unserialize((string) $entry->variables, ['allowed_classes' => false]);
                if (is_array($variables)
                    && Order::NODESHOP_ORDER_STATUS_COMPLETED === ($variables['@new_status'] ?? null)
                ) {
                    return (int) $entry->timestamp;
                }
            } elseif (null === $checkout_finished) {
                $checkout_finished = (int) $entry->timestamp;
            }
        }

        return $checkout_finished
            ?? (int) ($order->get('created')->value ?: $order->get('changed')->value);
    }

    /**
    * {@inheritdoc}
    */
    public function executeMultiple(array $entities)
    {
        foreach ($entities as $entity) {
            $this->execute($entity);
        }

        $export = new Export($this->export_data);
        $exportdata = $export->get();
        $exportstring = implode("\n", $exportdata);

        $response = new Response($exportstring);
        // the export contains personal data, so it must not be stored by any cache
        $response->setPrivate();
        $response->headers->addCacheControlDirective('no-store');

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'shopbest.csv'
        );

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', $disposition);

        // mark entities as exported.
        foreach ($entities as $entity) {
            if ($entity->hasField('exported')) {
                $entity->exported->value = true;
                $entity->addOrderHistoryEntry(
                    'Exported order',
                    []
                );
                $entity->save();
            }
        }

        $this->messenger()->addMessage(
            'Die Datensätze wurden exportiert und als exportiert markiert. ' .
            'Über die Suche können die Datensätze wieder gefunden und bei Bedarf erneut exportiert werden.'
        );

        // The values are exported unchanged, as the Verlags-DB does not cope with changed contents. Problems are
        // reported, so the orders can be checked before importing the file.
        foreach (array_merge($this->warnings, $export->getWarnings()) as $warning) {
            $this->getLogger('nodeshop_export')->warning($warning);
            $this->messenger()->addWarning($warning);
        }

        // Action plugins can not return a response, so the download is sent via an exception, that Drupal turns into
        // the response of the request. Unlike "exit" this lets Drupal finish the request, e.g. store the messages.
        throw new EnforcedResponseException($response);
    }

    /**
     * {@inheritdoc}
     */
    public function access($object, ?AccountInterface $account = null, $return_as_object = false)
    {
        $result = $object->access('export', $account, true);

        return $return_as_object ? $result : $result->isAllowed();
    }
}
