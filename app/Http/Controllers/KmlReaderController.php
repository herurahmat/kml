<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use DB;

class KmlReaderController extends Controller
{
    public function index()
    {
        $data=DB::table('contoh_data')->select('id','name',DB::Raw("AsText(poly) as poly_text"))->get();
        return view('home',compact('data'));
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $dump=$request->dump?TRUE:FALSE;
            $image = $request->file('file');
            $path = public_path('/');
            $extension  = strtolower($image->getClientOriginalExtension());
            $file_name   = 'kml' . '-' . date('Y-m-d') . '.' . $extension;

            if ($image->move($path, $file_name)) {
                $this->upload_do($path . $file_name, $dump);
            }

            return redirect('/');
        }
        return redirect('/');
    }

    public function upload_do($path_file,$dump=false)
    {
        if (file_exists($path_file) && is_file($path_file)) {
            DB::table('contoh_data')->truncate();
            $insert_batch = [];
            $file = $path_file;
            $xml = simplexml_load_file($file);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);


            $data_place = [];
            foreach ($array['Document']['Folder'] as $place) {
                if (isset($place['Placemark'])) {
                    $data_place[] = $place['Placemark'];
                }
            }
            if (count($data_place)) {
                foreach ($data_place as $place_item) {
                    foreach ($place_item as $place_item_item) {
                        $city = $place_item_item['name'];

                        if (isset($place_item_item['Polygon'])) {

                            $poly_data = $place_item_item['Polygon']['outerBoundaryIs']['LinearRing']['coordinates'];

                            $explode = explode("0\n", $poly_data);
                            $poly_item = [];
                            if (count($explode)) {
                                foreach ($explode as $e1) {
                                    if (!empty($e1)) {
                                        $poly_a=str_replace("\n","",$e1);
                                        $poly_a = str_replace(" ", "", $poly_a);
                                        $poly_a=substr($poly_a,0,-1);
                                        $poly_a=str_replace(","," ",$poly_a);
                                        if(!empty($poly_a))
                                        {
                                            $poly_item[] = $poly_a;
                                        }
                                    }
                                }
                                // if($city == 'JKT6')
                                // {
                                //     dd($poly_item, $city);
                                // }
                                $tambahan = "";
                                $count_explode=count($explode);
                                if(!empty(($explode[$count_explode - 1])))
                                {
                                    if($explode[0] != ($explode[$count_explode - 1]))
                                    {
                                        $tambahan = ",".str_replace(",", " ", $explode[0]);
                                        // dd("0", $explode[0], ($explode[$count_explode - 1]));
                                    }
                                }else{
                                    if ($explode[0] != ($explode[$count_explode - 2]))
                                    {
                                        $tambahan = "," . str_replace(",", " ", $explode[0]);
                                        // dd("1", $explode[0], ($explode[$count_explode - 2]));
                                    }
                                }

                                $polygon_data = implode(",", $poly_item) . $tambahan;
                                $insert_batch[] = [
                                    'name' => $city,
                                    'poly' => \DB::Raw("ST_PolygonFromText('POLYGON(($polygon_data))')"),
                                    // 'poly_item'=>$poly_item
                                ];
                            }
                        }
                    }
                }

                if ($dump == true)
                {
                    dd("Data All ",$data_place,"Data Insert",$insert_batch);
                }

                for($i=0;$i<count($insert_batch);$i++)
                {
                    try {
                        DB::table('contoh_data')->insert($insert_batch[$i]);
                    } catch (\Throwable $th) {
                        dd("Line ".$i, $insert_batch[$i],$th->getMessage());
                    }

                }

            }
        }
    }

    public function calculate(Request $request)
    {
        $longitude=$request->longitude;
        $latitude = $request->latitude;
        $data = DB::table('contoh_data')->select('name', DB::Raw("(ST_CONTAINS(poly,POINT(?,?))) as inside"))
        ->orderBy('inside', 'asc')
        ->setBindings([$longitude, $latitude])
        ->get();

        $output=[];
        if(count($data))
        {
            foreach($data as $row)
            {
                if($row->inside == 1)
                {
                    $output=$row;
                }
            }
        }

        return response()->json($output);
    }


}
