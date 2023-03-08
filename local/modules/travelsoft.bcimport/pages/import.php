<?
require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

@set_time_limit(0);
@ignore_user_abort(true);
?>
<script type="text/javascript" src="/local/modules/travelsoft.bcimport/assets/js/jquery-3.3.1.min.js"></script>

<script>
    $.ajax({
        url: "/local/modules/travelsoft.bcimport/ajax/getCities.php",
        success: function (cities) {
            sequence(cities, asyncFunction);
        }
    });

    function asyncFunction(city) {
        console.log(city, 'city');
        return $.ajax({
            url: "/local/modules/travelsoft.bcimport/ajax/import.php",
            data: {city: city},
            datatype: 'json',
            success: function (data) {
                console.log(data, 'data');
            }
        });
    }

    function sequence(arr, callback) {
        let i = 0;

        let request = function (item) {

            return callback(item).then(function () {
                if (i < arr.length - 1)
                    return request(arr[++i]);
            });
        };

        return request(arr[i]);
    }
</script>

<? //require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
