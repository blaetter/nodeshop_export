<?php

namespace Drupal\nodeshop_export\Export;

use Drupal\nodeshop_export\Export\RowItem;
use Drupal\nodeshop_export\Export\SkeletonItem;

/**
 * Class lightshop export row
 *  Handles the export of a single data row - e.g. a single order
 *
 */
class Row
{

    private $data;
    private $skeleton;
    private $header_only;

    // representation of the row
    private $row;

    public function __construct($data, $header_only = false)
    {
        $this->data = $data;
        $this->row = '';
        $this->header_only = $header_only;
        $this->create();
    }

    /**
     * Creates a row skeleton whith all needed values
     */
    private function create()
    {
        $this->skeleton = [
            new SkeletonItem('adressnummer', 'Adressnummer', false, '', '', 6, 'int'),
            new SkeletonItem('anrede', 'Anrede', true, '', '', 20, 'string'),
            new SkeletonItem('titel', 'Titel', true, '', '', 20, 'string'),
            new SkeletonItem('familienname', 'Familienname', true, '', '', 30, 'string'),
            new SkeletonItem('vorname', 'Vorname', true, '', '', 35, 'string'),
            //new SkeletonItem('namenszusatz1', '', false, '', '', 35, 'string'),
            //new SkeletonItem('namenszusatz2', '', false, '', '', 35, 'string'),
            // Neu in aktuellem Export: Firma 1 - 3
            // new SkeletonItem('firma_1', 'Firma 1', false, '', '', 35, 'string'),
            // new SkeletonItem('firma_2', 'Firma 2', false, '', '', 35, 'string'),
            // new SkeletonItem('firma_3', 'Firma 3', false, '', '', 35, 'string'),
            new SkeletonItem('land', 'Land ISO', true, '', '', 2, 'string'),
            new SkeletonItem('plz_strasse', 'Str.Plz', true, '', '', 6, 'string'),
            new SkeletonItem('plz_postfach', 'Pf.Plz', false, '', '', 5, 'string'),
            new SkeletonItem('plz_grosskunde', 'Großkd.Plz', false, '', '', 5, 'string'),
            //new SkeletonItem('leerstellen', '', false, '', '', 4, 'string'),
            new SkeletonItem('strasse', 'Straße', true, '', '', 35, 'string'),
            // Neu in aktuellem Export: Hausnummer
            //new SkeletonItem('hausnummer', 'Hausnummer', true, '', '', 10, 'string'),
            new SkeletonItem('ort', 'Ort', true, '', '', 30, 'string'),
            new SkeletonItem('postfach', 'Postfach', false, '', '', 11, 'string'),
            new SkeletonItem('umsatzsteuerid', 'Ust-IdNr', false, '', '', 15, 'string'),
            new SkeletonItem('telefonvorwahl', 'Telefon', false, '', '', 10, 'string'),
            new SkeletonItem('telefon', 'Telefon2', false, '', '', 25, 'string'),
            //new SkeletonItem('fax', '', false, '', '', 15, 'string'),
            new SkeletonItem('mobiltelefon', 'Handy', false, '', '', 25, 'string'),
            new SkeletonItem('email', 'E-Mail', true, '', '', 200, 'string'),
            //new SkeletonItem('rechnungpermail', '', true, '', '', 1, 'int'),
            // new SkeletonItem('bankname', '', false, '', '', 25, 'string'),
            // new SkeletonItem('blz', '', false, '', '', 8, 'int'),
            // new SkeletonItem('kontonummer', '', false, '', '', 11, 'int'),
            // new SkeletonItem('kontoinhaber', '', false, '', '', 20, 'string'),
            // Neu in aktuellem Export: Zahlungsweg und PayPalCode
            // new SkeletonItem('rechnungsart', 'Zahlungsweg', true, '', '', 34, 'string'),
            // new SkeletonItem('paypalcode', 'PayPalCode', false, '', '', 34, 'string'),
            new SkeletonItem('iban', 'IBAN', false, '', '', 34, 'string'),
            // Neu in aktuellem Export: BIC
            //new SkeletonItem('bic', 'BIC', false, '', '', 34, 'string'),
           // new SkeletonItem('swift', '', false, '', '', 11, 'string'),
            new SkeletonItem('rechnungsart', 'Zahlungsweg', true, '', '', 32, 'string'),
            // new SkeletonItem('kreditkartennummer', '', false, '', '', 16, 'int'),
            // new SkeletonItem('kreditkartebis', '', false, '', '', 4, 'int'),
            // new SkeletonItem('kreditkartepruefziffer', '', false, '', '', 4, 'int'),
            // Neu in aktuellem Export: Mandatsreferenz und -Datum
            //new SkeletonItem('mandatsreferenz', 'Mandatsreferenz', false, '', '', 1, 'int'),
            //new SkeletonItem('mandatsdatum', 'Mandatsdatum', false, '', '', 1, 'int'),
            new SkeletonItem('fremdsprache', 'Fremdsprache', false, '', '', 1, 'int'),
            new SkeletonItem('fremdwaehrung', 'Fremdwährung', false, '', '', 3, 'string'),
            new SkeletonItem('geburtsdatum', 'Datum JJJJMMTT', false, '', '', 8, 'int'),
            new SkeletonItem('kundengruppe', 'Kundengruppe', false, '', '', 2, 'string'),
            new SkeletonItem('kundenrabatt', 'Kundenrabatt %', false, '', '', 5, 'string'),
            //new SkeletonItem('passwort', '', false, '', '', 15, 'string'),
            // Neu in aktuellem Export: Shop-ID und Schulnummer
            //new SkeletonItem('shop-id', 'Shop-ID %', false, '', '', 5, 'string'),
            //new SkeletonItem('schulnummer', 'Schulnummer %', false, '', '', 5, 'string'),
            new SkeletonItem('adressmerkmal1', 'Adr.Merkmal 1', true, '', '', 45, 'string'),
            new SkeletonItem('adressmerkmal2', 'Adr.Merkmal 2', false, '', '', 45, 'string'),
            new SkeletonItem('adressmerkmal3', 'Adr.Merkmal 3', false, '', '', 45, 'string'),
            new SkeletonItem('adressmerkmal4', 'Adr.Merkmal 4', false, '', '', 45, 'string'),
            new SkeletonItem('adressmerkmal5', 'Adr.Merkmal 5', false, '', '', 45, 'string'),
            new SkeletonItem('adressmerkmal6', 'Adr.Merkmal 6', false, '', '', 45, 'string'),
            new SkeletonItem('adressmerkmal7', 'Adr.Merkmal 7', false, '', '', 45, 'string'),
            new SkeletonItem('adressmerkmal8', 'Adr.Merkmal 8', false, '', '', 45, 'string'),
            new SkeletonItem('adressmerkmal9', 'Adr.Merkmal 9', false, '', '', 45, 'string'),
            new SkeletonItem('adressmerkmal10', 'Adr.Merkmal 10', false, '', '', 45, 'string'),
            new SkeletonItem('kennzeichen1', 'Kennzeichen 1', false, '', '', 1, 'string'),
            new SkeletonItem('kennzeichen2', 'Kennzeichen 2', false, '', '', 1, 'string'),
            new SkeletonItem('kennzeichen3', 'Kennzeichen 3', false, '', '', 1, 'string'),
            new SkeletonItem('kennzeichen4', 'Kennzeichen 4', false, '', '', 1, 'string'),
            new SkeletonItem('kennzeichen5', 'Kennzeichen 5', false, '', '', 1, 'string'),
            new SkeletonItem('kennzeichen6', 'Kennzeichen 6', false, '', '', 1, 'string'),
            new SkeletonItem('kennzeichen7', 'Kennzeichen 7', false, '', '', 1, 'string'),
            new SkeletonItem('kennzeichen8', 'Kennzeichen 8', false, '', '', 1, 'string'),
            new SkeletonItem('kennzeichen9', 'Kennzeichen 9', false, '', '', 1, 'string'),
            // new SkeletonItem('leerstellen', 'Kennzeichen 10', false, '', '', 5, 'string'),
            // Neu in aktuellem Export: Zahlungsziel
            //new SkeletonItem('zahlungsziel', 'Zahlungsziel', false, '', '', 10, 'string'),
            //Abweichende Lieferadresse, falls
            new SkeletonItem('ladressnummer', 'LA-Adr.Nummer', false, '', '', 6, 'int'),
            new SkeletonItem('lanrede', 'LA-Anrede', true, '', '', 20, 'string'),
            new SkeletonItem('ltitel', 'LA-Titel', true, '', '', 20, 'string'),
            new SkeletonItem('lfamilienname', 'LA-Familienname', true, '', '', 30, 'string'),
            new SkeletonItem('lvorname', 'LA-Vorname', true, '', '', 30, 'string'),
            // new SkeletonItem('lnamenszusatz1', '', false, '', '', 35, 'string'),
            // new SkeletonItem('lnamenszusatz2', '', false, '', '', 35, 'string'),
            // Neu im aktuellen Export: LA-Firma 1 - 3
            // new SkeletonItem('lfirma_1', 'LA-Firma 1', false, '', '', 35, 'string'),
            // new SkeletonItem('lfirma_2', 'LA-Firma 2', false, '', '', 35, 'string'),
            // new SkeletonItem('lfirma_3', 'LA-Firma 3', false, '', '', 35, 'string'),
            new SkeletonItem('lland', 'LA-Land ISO', true, '', '', 2, 'string'),
            new SkeletonItem('lplz_strasse', 'LA-Str.Plz', true, '', '', 6, 'string'),
            new SkeletonItem('lplz_postfach', 'LA-Pf.Plz', false, '', '', 5, 'string'),
            new SkeletonItem('lplz_grosskunde', 'LA-Grosskd.Plz', false, '', '', 5, 'string'),
            // new SkeletonItem('lleerstellen', '', false, '', '', 4, 'string'),
            new SkeletonItem('lstrasse', 'LA-Straße', true, '', '', 35, 'string'),
            // Neu in aktuellem Export: LA Hausnummer und LA Hausnummernzusatz
            //new SkeletonItem('lhausnummer', 'LA-Hausnummer', true, '', '', 35, 'string'),
            //new SkeletonItem('lhausnummerzusatz', 'LA-Hausnummerzusatz', true, '', '', 35, 'string'),
            new SkeletonItem('lort', 'LA-Ort', true, '', '', 30, 'string'),
            new SkeletonItem('lpostfach', 'LA-Postfach', false, '', '', 11, 'string'),
            // Neu in aktuellem Export: LA-Shop-OF, LA-Telefon, LA-Telefon2, LA-Handy, LA-E-Mail
            //new SkeletonItem('shop-id', 'LA-Shop-ID %', false, '', '', 5, 'string'),
            //new SkeletonItem('ltelefonvorwahl', 'LA-Telefon', false, '', '', 10, 'string'),
            //new SkeletonItem('ltelefon', 'LA-Telefon2', false, '', '', 25, 'string'),
            //new SkeletonItem('lmobiltelefon', 'LA-Handy', false, '', '', 25, 'string'),
            //new SkeletonItem('lemail', 'LA-E-Mail', false, '', '', 200, 'string'),
            // new SkeletonItem('leerstelellen', '', false, '', '', 1, 'string'),
            //Bestellungen buecher
            new SkeletonItem('bartikelnummer', 'Artikelnummer', true, '', '', 10, 'string'),
            new SkeletonItem('bmenge', 'Bestellmenge', true, '', '', 4, 'int'),
            // Neu in aktuellem Export: Liefermenge und Dauerauftrag
            //new SkeletonItem('bliefermenge', 'Liefermenge', false, '', '', 4, 'int'),
            //new SkeletonItem('bdauerauftrag', 'Dauerauftrag', false, '', '', 1, 'int'),
            new SkeletonItem('bbestellzeichen', 'Bestellzeichen', false, '', '', 10, 'string'),
            new SkeletonItem('bbestelldatum', 'Bestelldatum JJJJMMTT', true, '', '', 8, 'int'),
            new SkeletonItem('bversandkosten_fix', 'Versandkosten fix', false, '', '', 6, 'int'),
            // Neu in aktuellem Export: BuchRechnung per E-Mail
            //new SkeletonItem('brechnung_mail', 'BuchRechnung per E-Mail', false, '', '', 1, 'int'),
            new SkeletonItem('bversandkostenjn', 'Versandkosten J/N', true, '', '', 1, 'string'),
            new SkeletonItem('einzelpreis', 'Einzelpreis', false, '', '', 9, 'int'),
            new SkeletonItem('posten_rabatt', 'Posten Rabatt %', false, '', '', 4, 'int'),
            //new SkeletonItem('ausgangslage_nr', '', false, '', '', 6, 'int'),
            new SkeletonItem('eillieferung', 'Eillieferung', false, '', '', 1, 'string'),
            // new SkeletonItem('leerstelellen', '', false, '', '', 1, 'string'),
            //Bestellungen abo
            // Neu in aktuellem Export: Abo-Nummer
            //new SkeletonItem('babonummer', 'Abo Nummer', false, '', '', 10, 'string'),
            new SkeletonItem('bzeitschriftnr', 'Zeitschrift Nummer', true, '', '', 2, 'int'),
            new SkeletonItem('banzahl_zahlen', 'Exemplare zu zahlen', false, '', '', 5, 'int'),
            new SkeletonItem('banzahl_kostenlos', 'Exemplare kostenlos', false, '', '', 5, 'int'),
            new SkeletonItem('bpreisnummer', 'Preisnummer', true, '', '', 2, 'int'),
            // Neu in aktuellem Export: Lieferung ab akt Ausgabe
            //new SkeletonItem('blieferung_ab_akt_ausgabe', 'Lieferung ab akt Ausgabe', false, '', '', 3, 'int'),
            new SkeletonItem('blieferung_ab_ausgabe', 'Lieferung ab Ausgabe', false, '', '', 3, 'int'),
            new SkeletonItem('blieferung_ab_jahr', 'Lieferung ab Jahr', false, '', '', 2, 'int'),
            new SkeletonItem('blieferung_bis_ausg', 'Lieferung bis Ausgabe', false, '', '', 3, 'int'),
            new SkeletonItem('blieferung_bis_jahr', 'Lieferung bis Jahr', false, '', '', 2, 'int'),
            // Neu in aktuellem Export: Anzahl Ausgaben, Nächste Rechnung Ausgabe, Nächste Rechnung Jahr
            //new SkeletonItem('banzahl_ausgaben', 'Anzahl Ausgaben', false, '', '', 4, 'int'),
            //new SkeletonItem('bnaechste_rechnung', 'Nächste Rechnung', false, '', '', 8, 'int'),
            //new SkeletonItem('bnaechste_rechnung_jahr', 'Nächste Rechnung Jahr', false, '', '', 4, 'int'),
            new SkeletonItem('brabatt', 'Rabatt %', false, '', '', 4, 'int'),
            // Neu in aktuellem Export: AboRechnung per E-Mail
            //new SkeletonItem('baborechnung_mail', 'AboRechnung per E-Mail', false, '', '', 1, 'int'),
            // new SkeletonItem('babbuchung_jn', '', false, '', '', 1, 'string'),
            // new SkeletonItem('bskonto', '', false, '', '', 3, 'int'),
            new SkeletonItem('brechnung_bei_abbuch', 'Rechnung bei Lastschrift-Einzug J/N', false, '', '', 1, 'string'),
            new SkeletonItem('banzahl_probe', 'Probeabo Ausgaben', false, '', '', 3, 'int'),
            new SkeletonItem('bprobeabo_preisnr', 'Probeabo Preisnummer', false, '', '', 2, 'int'),
            new SkeletonItem('brechnung_alle_x_ausg', 'Rechnung alle Ausgaben', false, '', '', 2, 'int'),
            // Neu in aktuellem Export: Quartalszahlweise
            //new SkeletonItem('bquartalszahlweise', 'Quartalszahlweise', false, '', '', 1, 'int'),
            new SkeletonItem('bversandkosten_ind', 'Abo Versandkosten', false, '', '', 8, 'int'),
            new SkeletonItem('babo1', 'Abo-Art1', false, '', '', 10, 'string'),
            new SkeletonItem('babo2', 'Abo-Art2', false, '', '', 10, 'string'),
            // Neu in aktuellem Export: Abo-Art3
            //new SkeletonItem('babo3', 'Abo-Art3', false, '', '', 10, 'string'),
            new SkeletonItem('bivw', 'IVW Meldeformular Feldnummer', false, '', '', 2, 'int'),
            new SkeletonItem('bherkunft_abo', 'Herkunft des Abos', false, '', '', 10, 'string'),
            new SkeletonItem('bbestellzeichen', 'Abo Bestellzeichen', false, '', '', 50, 'string'),
            // Neu in aktuellem Export: Abo Bemerkungen
            //new SkeletonItem('bbemerkungen', 'Abo Bemerkungen', false, '', '', 100, 'string'),
            //Abo-Praemie
            new SkeletonItem('partikelnr', 'Prämie-Artikel', false, '', '', 10, 'string'),
            new SkeletonItem('pzuzahlung', 'Prämien Zuzahlung', false, '', '', 6, 'int'),
            new SkeletonItem('pnachzahlung', 'Prämie nach Abo Zahlung versenden', false, '', '', 1, 'int'),
            //praemienempfaenger
            new SkeletonItem('padressnr', 'Prämienempf. Adressnummer', false, '', '', 6, 'int'),
            new SkeletonItem('panrede', 'Prämienempf. Anrede', false, '', '', 20, 'string'),
            new SkeletonItem('ptitel', 'Prämienempf. Titel', false, '', '', 20, 'string'),
            new SkeletonItem('pfamilienname', 'Prämienempf. Familienname', false, '', '', 30, 'string'),
            new SkeletonItem('pvorname', '', false, 'Prämienempf. Vorname', '', 30, 'string'),
            // new SkeletonItem('pnamenszusatz1', '', false, '', '', 35, 'string'),
            // new SkeletonItem('pnamenszusatz2', '', false, '', '', 35, 'string'),
            // Neu im aktuellen Export: Prämienempf.-Firma 1 - 3
            // new SkeletonItem('pfirma_1', 'Prämienempf. Firma 1', false, '', '', 35, 'string'),
            // new SkeletonItem('pfirma_2', 'Prämienempf. Firma 2', false, '', '', 35, 'string'),
            // new SkeletonItem('pfirma_3', 'Prämienempf. Firma 3', false, '', '', 35, 'string'),
            new SkeletonItem('pland', 'Prämienempf. Land ISO', false, '', '', 2, 'string'),
            new SkeletonItem('pplz_strasse', 'Prämienempf. Plz Straße', false, '', '', 6, 'string'),
            new SkeletonItem('pstrasse', 'Prämienempf. Straße', false, '', '', 35, 'string'),
            // Neu in aktuellem Export: Prämienempf. Hausnummer und Prämienempf. Hausnummernzusatz
            //new SkeletonItem('phausnummer', 'Prämienempf. Hausnummer', true, '', '', 35, 'string'),
            //new SkeletonItem('phausnummerzusatz', 'Prämienempf. Hausnummerzusatz', true, '', '', 35, 'string'),
            new SkeletonItem('port', 'Prämienempf. Ort', false, '', '', 30, 'string'),
            new SkeletonItem('panadresse', 'Prämiene an Adresse', false, '', '', 1, 'int'),
            new SkeletonItem('pkostenlos', 'Prämie kostenlos', false, '', '', 1, 'string'),
            new SkeletonItem('versandkosten', 'Prämieversandkosten', false, '', '', 6, 'int'),
            //Adressinfos und Zusatzfelder
            new SkeletonItem('feld1', 'Adr.Info 1', false, '', '', 26, 'string'),
            new SkeletonItem('feld2', 'Adr.Info 2', false, '', '', 26, 'string'),
            new SkeletonItem('feld3', 'Adr.Info 3', false, '', '', 26, 'string'),
            new SkeletonItem('feld4', 'Adr.Info 4', false, '', '', 26, 'string'),
            new SkeletonItem('feld5', 'Adr.Info 5', false, '', '', 26, 'string'),
            new SkeletonItem('feld6', 'Adr.Info 6', false, '', '', 26, 'string'),
            new SkeletonItem('feld7', 'Adr.Info 7', false, '', '', 26, 'string'),
            new SkeletonItem('feld8', 'Adr.Info 8', false, '', '', 26, 'string'),
            new SkeletonItem('feld9', 'Adr.Info 9', false, '', '', 26, 'string'),
            new SkeletonItem('feld10', 'Adr.Info 10', false, '', '', 26, 'string'),
            new SkeletonItem('feld11', 'Adr.Info 11', false, '', '', 26, 'string'),
            new SkeletonItem('feld12', 'Adr.Info 12', false, '', '', 26, 'string'),
            new SkeletonItem('feld13', 'Adr.Info 13', false, '', '', 26, 'string'),
            new SkeletonItem('feld14', 'Adr.Info 14', false, '', '', 26, 'string'),
            new SkeletonItem('feld15', 'Adr.Info 15', false, '', '', 26, 'string'),
            new SkeletonItem('feld16', 'Adr.Info 16', false, '', '', 26, 'string'),
            // new SkeletonItem('text1', '', false, '', '', 76, 'string'),
            // new SkeletonItem('text2', '', false, '', '', 76, 'string'),
            // new SkeletonItem('text3', '', false, '', '', 76, 'string'),
            // new SkeletonItem('text4', '', false, '', '', 76, 'string'),
            // new SkeletonItem('text5', '', false, '', '', 76, 'string'),
            // new SkeletonItem('text6', '', false, '', '', 76, 'string'),
            // new SkeletonItem('text7', '', false, '', '', 76, 'string'),
            // new SkeletonItem('text8', '', false, '', '', 76, 'string'),
        ];

        if (true == $this->header_only) {
            $this->header();
        } else {
            $this->set();
        }
    }

    /**
     * Sets the header row if requested
     *
     * @return void
     */
    private function header()
    {
        foreach ($this->skeleton as $field) {
          // we prepare the header row with the same methods as the rows of
          // content and are using the same skeletonItems as for the content.
          // To make validation work, we set some values manually because
          // the header row only contains strings.
          $item = new RowItem(
              $field->key,
              128, // max length
              'string', // type
              $field->validation,
              $field->expression
          );
          try {
              // We need to check if item is accessable so we get only
              // headers with content in the header_row.
              // We need to set encoding to ASCII here for some reasons....
              // and we use the csv_title from the skeleton here as we
              // build the header row.
              if (false != $field->accessable) {
                $item->set($field->csv_title);
                $this->row .= $item->get();
              }
          } catch (\Exception $e) {
              echo 'Exception abgefangen header: ',  $e->getMessage(), "\n";
          }
        }
    }

    /**
     * Sets a row
     *
     * @return void
     */
    private function set()
    {
        $count = 1;
        foreach ($this->skeleton as $field) {
          $item = new RowItem(
              $field->key,
              $field->length,
              $field->type,
              $field->validation,
              $field->expression
          );
          try {
              if (false != $field->accessable) {
                  $value_to_set = $this->data[$field->key];
                  if (empty($value_to_set)) {
                      $value_to_set = '';
                  }
                  $item->set($value_to_set);
                  $this->row .= $item->get();
              }
          } catch (\Exception $e) {
              echo 'Exception abgefangen set: ',  $e->getMessage(), "\n";
          }
        }
    }

    /**
     * Get a single row
     *
     * @return string
     *  The row
     */
    public function get()
    {
        return $this->row;
    }

}
