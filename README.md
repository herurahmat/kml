Laravel 8 <br/>
MySQL 5.6 <br/>

clone, migrate. Done <br/>

1. default route : Read KML from public folder dki-kota.kml <br/>
a. Simple XML Load : KmlReaderController <br/>

```
SimpleXMLElement {#274 ▼
  +"@attributes": array:1 [▶]
  +"name": "dki_kota.1"
  +"description": """
    <h4>dki_kota</h4>
    
    <ul class="textattributes">
      
      <li><strong><span class="atr-name">KAB_NAME</span>:</strong> <span class="atr-value">JAKARTA BARAT</span></li>
      <li><strong><span class="atr-name">Kota</span>:</strong> <span class="atr-value">Jakarta Barat</span></li>
    </ul>
    """
  +"LookAt": SimpleXMLElement {#283 ▶}
  +"Style": SimpleXMLElement {#284 ▶}
  +"MultiGeometry": SimpleXMLElement {#285 ▶}
}
```

<br/>

Ini tergantung KML nya. Pada function getElementByClass ada membaca attribute class atr-value <br/>

b. Ambil data kota dan koordinat dan insert. Polygon atau Point : <br/>
Polygon : <br/>
```php
<?php
$r->MultiGeometry->MultiGeometry->Polygon[0]->outerBoundaryIs->LinearRing->coordinates[0]
?>
```
<br/>

Point : <br/>
```php
<?php
$r->MultiGeometry->Point->coordinates[0]
?>
```
<br/>
3. Kalkulasi jarak yg terdekat : <br/>
```php
<?php
$longitude = 106.8971442;
$latitude = -6.2236169;
$data=DB::table('contoh_data')->select('name',DB::Raw("(ST_Distance_Sphere(latlng,POINT(?,?))) as distance"))
->orderBy('distance','asc')
->setBindings([$longitude,$latitude])
->get();
?>
```
