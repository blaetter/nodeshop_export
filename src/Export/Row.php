<?php

namespace Drupal\nodeshop_export\Export;

use Drupal\nodeshop_export\Export\RowItem;

/**
 * Class lightshop export row
 *  Handles the export of a single data row - e.g. a single order
 *
 */
class Row
{

    // representation of the row
    private $row;

    public function __construct($data)
    {
        $this->data = $data;
        $this->row = '';
        $this->create();
    }

    /**
     * Creates a row skeleton whith all needed values
     *
     */
    private function create()
    {
        $this->skeleton = array(
        array('key' => 'adressnummer',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'),
        array('key' => 'anrede',                  'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'),
        array('key' => 'titel',                   'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'),
        array('key' => 'familienname',            'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'),
        array('key' => 'vorname',                 'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'),
        array('key' => 'namenszusatz1',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'),
        array('key' => 'namenszusatz2',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'),
        array('key' => 'land',                    'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'string'),
        array('key' => 'plz_strasse',             'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'string'),
        array('key' => 'plz_postfach',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'),
        array('key' => 'plz_grosskunde',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'),
        array('key' => 'leerstellen',             'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'string'),
        array('key' => 'strasse',                 'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'),
        array('key' => 'ort',                     'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'),
        array('key' => 'postfach',                'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 11,   'type' => 'string'),
        array('key' => 'umsatzsteuerid',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 15,   'type' => 'string'),
        array('key' => 'telefonvorwahl',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'),
        array('key' => 'telefon',                 'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 25,   'type' => 'string'),
        array('key' => 'fax',                     'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 15,   'type' => 'string'),
        array('key' => 'mobiltelefon',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 25,   'type' => 'string'),
        array('key' => 'email',                   'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 200,  'type' => 'string'),
        array('key' => 'rechnungpermail',         'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'int'),
        array('key' => 'bankname',                'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 25,   'type' => 'string'),
        array('key' => 'blz',                     'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 8,    'type' => 'int'),
        array('key' => 'kontonummer',             'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 11,    'type' => 'int'),
        array('key' => 'kontoinhaber',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'),
        array('key' => 'iban',                    'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 34,   'type' => 'string'),
        array('key' => 'swift',                   'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 11,   'type' => 'string'),
        array('key' => 'rechnungsart',            'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'),
        array('key' => 'kreditkartennummer',      'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 16,   'type' => 'int'),
        array('key' => 'kreditkartebis',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'int'),
        array('key' => 'kreditkartepruefziffer',  'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'int'),
        array('key' => 'fremdsprache',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'int'),
        array('key' => 'fremdwaehrung',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 3,    'type' => 'string'),
        array('key' => 'geburtsdatum',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 8,    'type' => 'int'),
        array('key' => 'kundengruppe',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'string'),
        array('key' => 'kundenrabatt',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'),
        array('key' => 'passwort',                'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 15,   'type' => 'string'),
        array('key' => 'adressmerkmal1',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'),
        array('key' => 'adressmerkmal2',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'),
        array('key' => 'adressmerkmal3',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'),
        array('key' => 'adressmerkmal4',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'),
        array('key' => 'adressmerkmal5',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'),
        array('key' => 'adressmerkmal6',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'),
        array('key' => 'adressmerkmal7',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'),
        array('key' => 'adressmerkmal8',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'),
        array('key' => 'adressmerkmal9',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'),
        array('key' => 'adressmerkmal10',         'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'),
        array('key' => 'kennzeichen1',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'kennzeichen2',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'kennzeichen3',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'kennzeichen4',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'kennzeichen5',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'kennzeichen6',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'kennzeichen7',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'kennzeichen8',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'kennzeichen9',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'leerstellen',             'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'),
        //Abweichende Lieferadresse, falls
        array('key' => 'ladressnummer',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'),
        array('key' => 'lanrede',                 'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'),
        array('key' => 'ltitel',                  'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'),
        array('key' => 'lfamilienname',           'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'),
        array('key' => 'lvorname',                'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'),
        array('key' => 'lnamenszusatz1',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'),
        array('key' => 'lnamenszusatz2',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'),
        array('key' => 'lland',                   'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'string'),
        array('key' => 'lplz_strasse',            'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'string'),
        array('key' => 'lplz_postfach',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'),
        array('key' => 'lplz_grosskunde',         'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'),
        array('key' => 'lleerstellen',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'string'),
        array('key' => 'lstrasse',                'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'),
        array('key' => 'lort',                    'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'),
        array('key' => 'lpostfach',               'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 11,   'type' => 'string'),
        array('key' => 'lerstelellen',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        //Bestellungen buecher
        array('key' => 'bartikelnummer',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'),
        array('key' => 'bmenge',                  'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'int'),
        array('key' => 'bbestellzeichen',         'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'),
        array('key' => 'bbestelldatum',           'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 8,    'type' => 'int'),
        array('key' => 'bversandkosten_fix',      'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'),
        array('key' => 'bversandkostenjn',        'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'einzelpreis',             'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 9,    'type' => 'int'),
        array('key' => 'posten_rabatt',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'int'),
        array('key' => 'ausgangslage_nr',         'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'),
        array('key' => 'eillieferung',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'lerstelellen',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        //Bestellungen abo
        array('key' => 'bzeitschriftnr',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'),
        array('key' => 'banzahl_zahlen',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'int'),
        array('key' => 'banzahl_kostenlos',       'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'int'),
        array('key' => 'bpreisnummer',            'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'),
        array('key' => 'blieferung_ab_ausgabe',   'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 3,    'type' => 'int'),
        array('key' => 'blieferung_ab_jahr',      'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'),
        array('key' => 'blieferung_bis_ausg',     'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 3,    'type' => 'int'),
        array('key' => 'blieferung_bis_jahr',     'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'),
        array('key' => 'brabatt',                 'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'int'),
        array('key' => 'babbuchung_jn',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'bskonto',                 'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 3,    'type' => 'int'),
        array('key' => 'brechnung_bei_abbuch',    'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'banzahl_probe',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 3,    'type' => 'int'),
        array('key' => 'bprobeabo_preisnr',       'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'),
        array('key' => 'brechnung_alle_x_ausg',   'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'),
        array('key' => 'bversandkosten_ind',      'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 8,    'type' => 'int'),
        array('key' => 'babo1',                   'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'),
        array('key' => 'babo2',                   'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'),
        array('key' => 'bivw',                    'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'),
        array('key' => 'bherkunft_abo',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'),
        array('key' => 'bbestellzeichen',         'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 50,   'type' => 'string'),
        //Abo-Praemie
        array('key' => 'partikelnr',              'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'),
        array('key' => 'pzuzahlung',              'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'),
        array('key' => 'pnachzahlung',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'int'),
        //praemienempfaenger
        array('key' => 'padressnr',               'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'),
        array('key' => 'panrede',                 'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'),
        array('key' => 'ptitel',                  'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'),
        array('key' => 'pfamilienname',           'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'),
        array('key' => 'pvorname',                'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'),
        array('key' => 'pnamenszusatz1',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'),
        array('key' => 'pnamenszusatz2',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'),
        array('key' => 'pland',                   'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'string'),
        array('key' => 'pplz_strasse',            'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'string'),
        array('key' => 'pstrasse',                'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'),
        array('key' => 'port',                    'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'),
        array('key' => 'panadresse',              'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'int'),
        array('key' => 'pkostenlos',              'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'),
        array('key' => 'versandkosten',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'),
        //Adressinfos und Zusatzfelder
        array('key' => 'feld1',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld2',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld3',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld4',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld5',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld6',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld7',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld8',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld9',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld10',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld11',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld12',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld13',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld14',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld15',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'feld16',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'),
        array('key' => 'text1',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'),
        array('key' => 'text2',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'),
        array('key' => 'text3',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'),
        array('key' => 'text4',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'),
        array('key' => 'text5',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'),
        array('key' => 'text6',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'),
        array('key' => 'text7',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'),
        array('key' => 'text8',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'),

        );


        $this->set();

    }

    /**
     * Sets a row
     *
     * @return
     *  The value of the item
     */
    private function set()
    {
        $count = 1;
        foreach ($this->skeleton as $field) {
            $item = new RowItem($field['key'], $field['length'], $field['type'], $field['validation'], $field['expression']);
            try {
                if (false != $field['accessable'] && isset($this->data[$field['key']]) && !empty($this->data[$field['key']])) {
                //We need to set encoding to ASCII here for some reasons....
                    $item->set(mb_convert_encoding($this->data[$field['key']], 'latin1'));
                }
            } catch (Exception $e) {
                echo 'Exception abgefangen: ',  $e->getMessage(), "\n";
            }
            $this->row .= $item->get();
        }
    }

    /**
     * Get a single row
     *
     * @return
     *  The row
     */
    public function get()
    {
        return $this->row;
    }
}
