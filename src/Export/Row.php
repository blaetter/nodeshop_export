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

    private $data;
    private $skeleton;

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
     */
    private function create()
    {
        $this->skeleton = [
            ['key' => 'adressnummer',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'],
            ['key' => 'anrede',                  'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'],
            ['key' => 'titel',                   'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'],
            ['key' => 'familienname',            'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'],
            ['key' => 'vorname',                 'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'],
            ['key' => 'namenszusatz1',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'],
            ['key' => 'namenszusatz2',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'],
            ['key' => 'land',                    'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'string'],
            ['key' => 'plz_strasse',             'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'string'],
            ['key' => 'plz_postfach',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'],
            ['key' => 'plz_grosskunde',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'],
            ['key' => 'leerstellen',             'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'string'],
            ['key' => 'strasse',                 'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'],
            ['key' => 'ort',                     'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'],
            ['key' => 'postfach',                'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 11,   'type' => 'string'],
            ['key' => 'umsatzsteuerid',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 15,   'type' => 'string'],
            ['key' => 'telefonvorwahl',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'],
            ['key' => 'telefon',                 'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 25,   'type' => 'string'],
            ['key' => 'fax',                     'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 15,   'type' => 'string'],
            ['key' => 'mobiltelefon',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 25,   'type' => 'string'],
            ['key' => 'email',                   'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 200,  'type' => 'string'],
            ['key' => 'rechnungpermail',         'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'int'],
            ['key' => 'bankname',                'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 25,   'type' => 'string'],
            ['key' => 'blz',                     'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 8,    'type' => 'int'],
            ['key' => 'kontonummer',             'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 11,    'type' => 'int'],
            ['key' => 'kontoinhaber',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'],
            ['key' => 'iban',                    'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 34,   'type' => 'string'],
            ['key' => 'swift',                   'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 11,   'type' => 'string'],
            ['key' => 'rechnungsart',            'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'],
            ['key' => 'kreditkartennummer',      'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 16,   'type' => 'int'],
            ['key' => 'kreditkartebis',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'int'],
            ['key' => 'kreditkartepruefziffer',  'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'int'],
            ['key' => 'fremdsprache',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'int'],
            ['key' => 'fremdwaehrung',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 3,    'type' => 'string'],
            ['key' => 'geburtsdatum',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 8,    'type' => 'int'],
            ['key' => 'kundengruppe',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'string'],
            ['key' => 'kundenrabatt',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'],
            ['key' => 'passwort',                'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 15,   'type' => 'string'],
            ['key' => 'adressmerkmal1',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'],
            ['key' => 'adressmerkmal2',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'],
            ['key' => 'adressmerkmal3',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'],
            ['key' => 'adressmerkmal4',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'],
            ['key' => 'adressmerkmal5',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'],
            ['key' => 'adressmerkmal6',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'],
            ['key' => 'adressmerkmal7',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'],
            ['key' => 'adressmerkmal8',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'],
            ['key' => 'adressmerkmal9',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'],
            ['key' => 'adressmerkmal10',         'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 45,   'type' => 'string'],
            ['key' => 'kennzeichen1',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'kennzeichen2',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'kennzeichen3',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'kennzeichen4',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'kennzeichen5',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'kennzeichen6',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'kennzeichen7',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'kennzeichen8',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'kennzeichen9',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'leerstellen',             'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'],
            //Abweichende Lieferadresse, falls
            ['key' => 'ladressnummer',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'],
            ['key' => 'lanrede',                 'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'],
            ['key' => 'ltitel',                  'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'],
            ['key' => 'lfamilienname',           'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'],
            ['key' => 'lvorname',                'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'],
            ['key' => 'lnamenszusatz1',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'],
            ['key' => 'lnamenszusatz2',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'],
            ['key' => 'lland',                   'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'string'],
            ['key' => 'lplz_strasse',            'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'string'],
            ['key' => 'lplz_postfach',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'],
            ['key' => 'lplz_grosskunde',         'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'string'],
            ['key' => 'lleerstellen',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'string'],
            ['key' => 'lstrasse',                'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'],
            ['key' => 'lort',                    'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'],
            ['key' => 'lpostfach',               'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 11,   'type' => 'string'],
            ['key' => 'lerstelellen',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            //Bestellungen buecher
            ['key' => 'bartikelnummer',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'],
            ['key' => 'bmenge',                  'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'int'],
            ['key' => 'bbestellzeichen',         'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'],
            ['key' => 'bbestelldatum',           'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 8,    'type' => 'int'],
            ['key' => 'bversandkosten_fix',      'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'],
            ['key' => 'bversandkostenjn',        'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'einzelpreis',             'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 9,    'type' => 'int'],
            ['key' => 'posten_rabatt',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'int'],
            ['key' => 'ausgangslage_nr',         'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'],
            ['key' => 'eillieferung',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'lerstelellen',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            //Bestellungen abo
            ['key' => 'bzeitschriftnr',          'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'],
            ['key' => 'banzahl_zahlen',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'int'],
            ['key' => 'banzahl_kostenlos',       'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 5,    'type' => 'int'],
            ['key' => 'bpreisnummer',            'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'],
            ['key' => 'blieferung_ab_ausgabe',   'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 3,    'type' => 'int'],
            ['key' => 'blieferung_ab_jahr',      'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'],
            ['key' => 'blieferung_bis_ausg',     'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 3,    'type' => 'int'],
            ['key' => 'blieferung_bis_jahr',     'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'],
            ['key' => 'brabatt',                 'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 4,    'type' => 'int'],
            ['key' => 'babbuchung_jn',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'bskonto',                 'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 3,    'type' => 'int'],
            ['key' => 'brechnung_bei_abbuch',    'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'banzahl_probe',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 3,    'type' => 'int'],
            ['key' => 'bprobeabo_preisnr',       'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'],
            ['key' => 'brechnung_alle_x_ausg',   'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'],
            ['key' => 'bversandkosten_ind',      'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 8,    'type' => 'int'],
            ['key' => 'babo1',                   'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'],
            ['key' => 'babo2',                   'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'],
            ['key' => 'bivw',                    'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'int'],
            ['key' => 'bherkunft_abo',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'],
            ['key' => 'bbestellzeichen',         'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 50,   'type' => 'string'],
            //Abo-Praemie
            ['key' => 'partikelnr',              'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 10,   'type' => 'string'],
            ['key' => 'pzuzahlung',              'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'],
            ['key' => 'pnachzahlung',            'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'int'],
            //praemienempfaenger
            ['key' => 'padressnr',               'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'],
            ['key' => 'panrede',                 'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'],
            ['key' => 'ptitel',                  'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 20,   'type' => 'string'],
            ['key' => 'pfamilienname',           'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'],
            ['key' => 'pvorname',                'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'],
            ['key' => 'pnamenszusatz1',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'],
            ['key' => 'pnamenszusatz2',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'],
            ['key' => 'pland',                   'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 2,    'type' => 'string'],
            ['key' => 'pplz_strasse',            'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'string'],
            ['key' => 'pstrasse',                'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 35,   'type' => 'string'],
            ['key' => 'port',                    'accessable' => true,   'validation' => '', 'expression' => '', 'length' => 30,   'type' => 'string'],
            ['key' => 'panadresse',              'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'int'],
            ['key' => 'pkostenlos',              'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 1,    'type' => 'string'],
            ['key' => 'versandkosten',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 6,    'type' => 'int'],
            //Adressinfos und Zusatzfelder
            ['key' => 'feld1',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld2',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld3',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld4',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld5',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld6',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld7',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld8',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld9',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld10',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld11',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld12',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld13',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld14',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld15',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'feld16',          'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 26,    'type' => 'string'],
            ['key' => 'text1',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'],
            ['key' => 'text2',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'],
            ['key' => 'text3',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'],
            ['key' => 'text4',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'],
            ['key' => 'text5',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'],
            ['key' => 'text6',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'],
            ['key' => 'text7',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'],
            ['key' => 'text8',           'accessable' => false,  'validation' => '', 'expression' => '', 'length' => 76,    'type' => 'string'],
        ];
        $this->set();
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
                $field['key'],
                $field['length'],
                $field['type'],
                $field['validation'],
                $field['expression']
            );
            try {
                if (false != $field['accessable']
                    && isset($this->data[$field['key']])
                    && !empty($this->data[$field['key']])
                ) {
                //We need to set encoding to ASCII here for some reasons....
                    $item->set(mb_convert_encoding($this->data[$field['key']], 'latin1'));
                }
            } catch (\Exception $e) {
                echo 'Exception abgefangen: ',  $e->getMessage(), "\n";
            }
            $this->row .= $item->get();
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
