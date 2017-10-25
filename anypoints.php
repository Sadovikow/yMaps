<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<?
	$city = array();
	$json = array();
	$arSelect = Array("ID", "IBLOCK_ID","DETAIL_PICTURE", "NAME", "PROPERTY_*");
	$arFilter = Array("IBLOCK_ID"=>IBLOCK_POINTS_ID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
	$res = CIBlockElement::GetList(Array("PROPERTY_CITY_VALUE" => "ASC"), $arFilter, false, false, $arSelect);
	while($ob = $res->GetNextElement()){ 
	 $arFields = $ob->GetFields();  
	 $arProps = $ob->GetProperties();
	 $arFields['DETAIL_PICTURE'] = CFile::GetPath($arFields['DETAIL_PICTURE']);
	 if($arProps['MAIN']['VALUE'] == 'Y'){
		 $city[$arFields['ID']] = $arFields;
		 $city[$arFields['ID']]['PROP'] = $arProps;
	 }
	 $json[$arFields['ID']]['COORDINATES'] = array($arProps['LATITUDE']['VALUE'],$arProps['LONGITUDE']['VALUE']);
	 $json[$arFields['ID']]['NAME'] = $arFields['NAME'];
	 $json[$arFields['ID']]['ADDRESS'] = $arProps['ADDRESS']['VALUE'];
	 $json[$arFields['ID']]['DETAIL_PICTURE'] = $arFields['DETAIL_PICTURE'];
	 $json[$arFields['ID']]['EMAIL'] = $arProps['EMAIL']['VALUE'];
	 $json[$arFields['ID']]['PHONE'] = $arProps['PHONE']['VALUE'];
	}

$json = json_encode($json); 
?>

	<!-- Карта -->
<script>
    var moscow_map;
	var result = $.parseJSON('<?php echo $json;?>');
    ymaps.ready(function(){
        moscow_map = new ymaps.Map("mappoints", {
			center: [55.75, 37.61],
            zoom: 8,
			controls: []
        });
		moscow_map.controls.add('zoomControl');
		moscow_map.controls.add('fullscreenControl');
		var myPlacemark, phone;


		for (var key in result) {
				phone = '';
				for (var value in result[key]['PHONE']) {
					phone += '<div class="distributor-phone">'+result[key]['PHONE'][value]+'</div>';
				}
				myPlacemark = new ymaps.Placemark(result[key]['COORDINATES'], 
				{

	balloonContentBody: '<div class="pointwindow">'+
						'<div class="pointwindow__logo"><img src="'+result[key]['DETAIL_PICTURE']+'"></div>'+
						'<div class="pointwindow__content">'+
							'<div class="pointwindow__content__name">'+result[key]['NAME']+'</div>'+
							'<div class="pointwindow__content__address>'+result[key]['ADDRESS']+'</div>'+
						'</div><div class="pointwindow__contactinfo"><div>Контактная информация</div>'+
							phone+
						'</div>'+
					'</div>',

				}, 
				{preset: 'islands#greenIcon', balloonMaxWidth: '600px',});
				moscow_map.geoObjects.add(myPlacemark);
		}
    });
</script>
<div id="mappoints" style="width:100%; height:500px"></div>
	<!-- Карта -->
