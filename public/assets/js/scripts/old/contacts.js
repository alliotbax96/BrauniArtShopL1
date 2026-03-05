var points = $(".point");

ymaps.ready(init);		
function init() {
	var myMap = new ymaps.Map("map", {
		center: [55.76, 37.64],
		zoom: 10
	}, {
		searchControlProvider: 'yandex#search'
	});
 
    $.each(points, function(key, value){
        ymaps.geocode($(value).val()).then(function (res) {
            var coord = res.geoObjects.get(0).geometry.getCoordinates();
            var myPlacemark = new ymaps.Placemark(coord, {
                iconContent: $(value).attr('data-name')
            }, {
                preset: 'islands#darkOrangeStretchyIcon'
            });
            myMap.geoObjects.add(myPlacemark);    	
        });
    });
    
}