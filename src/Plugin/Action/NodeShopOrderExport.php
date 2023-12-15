<?php

namespace Drupal\nodeshop_export\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
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
    use MessengerTrait;
    use StringTranslationTrait;

    /**
     * Holds the export data
     *
     * @var array
     */
    private $export_data;

    /**
     * {@inheritdoc}
     */
    public function execute(Order $order = null)
    {
        foreach ($order->getOrderProducts() as $order_product) {
            //Means OrderArray - not elegant but usefull
            $node = $order_product->get('nid')->entity;
            // $oa = array_merge($row, $entry, array('order'=>$o, 'node' => $node));
            $export = [];
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
                $export['bbestelldatum'] = \Drupal::service('date.formatter')->format($order->get('changed')->value, 'custom', 'Ymd');
                $export['bversandkosten_fix'] = '000000';
                $export['bversandkostenjn'] = 'n';
            } else {
              //Bestellungen abo
                $export['bzeitschriftnr'] = Helper::getValue($node, 'field_warenid');
                $export['bpreisnummer'] = Helper::getValue($node, 'field_preisnr');
                $export['bbestelldatum'] = \Drupal::service('date.formatter')->format($order->get('changed')->value, 'custom', 'Ymd');
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

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'shopbest.csv'
        );

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', $disposition);

        $response->send();

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

        // we cut hard here, so no extra content is transmitted from Drupal
        // it would be quite nice, if there is another way to handle the request
        // but as long as action plugins does not transfer return values to the
        // end user, we need to do it this way.
        exit;
    }

    /**
     * {@inheritdoc}
     */
    public function access($object, AccountInterface $account = null, $return_as_object = false)
    {
        $result = $object->access('export', $account, true);

        return $return_as_object ? $result : $result->isAllowed();
    }
}
