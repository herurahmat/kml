<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use DB;

class KmlReaderController extends Controller
{

    public function index()
    {
        DB::table('contoh_data')->truncate();
        $insert_batch=[];
        $file=public_path('dki-kota.kml');
        $xml=simplexml_load_file($file);
        foreach($xml->Document->Folder->Placemark as $r)
        {
            $latlng= $r->MultiGeometry->Point->coordinates[0];
            $polygon="";
            if(isset($r->MultiGeometry->MultiGeometry->Polygon[0]->outerBoundaryIs->LinearRing->coordinates[0]))
            {
                $polygon= $r->MultiGeometry->MultiGeometry->Polygon[0]->outerBoundaryIs->LinearRing->coordinates[0];
            }else{
                //Center
                if(isset($r->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates[0]))
                {
                    $polygon = $r->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates[0];
                }
            }
            $kota=$this->getElementByClass($r->description, 'atr-value');

            // koordinat terakhir harus sama dengan koordinat pertama
            $explode=explode(",",$polygon);
            unset($explode[0]);
            array_pop($explode);
            $last_item=explode(" ",$explode[1]);
            $cor_d = implode(",", $explode).', '.$last_item[0].' '.$last_item[1];
            $insert_batch[]=[
                'name'=>$kota[0],
                'poly'=> \DB::Raw("ST_PolygonFromText('POLYGON(($cor_d))')"),
                'latlng'=>\DB::Raw("POINT($latlng)")
            ];
        }
        DB::table('contoh_data')->insert($insert_batch);


    }

    function getElementByClass($string_html,$classname)
    {
        $dom_doc=new \DOMDocument();
        $dom_doc->loadHtml($string_html);
        $a = new \DOMXPath($dom_doc);
        $spans = $a->query("//*[contains(@class, '$classname')]");
        for ($i = $spans->length - 1; $i > -1; $i--) {
            $result[] = $spans->item($i)->firstChild->nodeValue;
        }

        return $result;
    }

}
