require([
    'jquery'
], function ($) {
    'use strict';
    console.log("Check Zipcode");
    
    $(document).on('keyup', "[name='postcode']", function () { 
        if($(this).val().length >= 5 ){

            var zip = $(this).val();
            $.ajax({
                    url: 'http://localhost/Latestmagento/pincodechecker/index/zipcode',
                    type: 'POST',
                    data: { zipcode: zip } ,
                    success: function (data) {
                        if(data != ''){
                            $("#zipcodeerror").hide();
                            $("[name='postcode']").after('<span id="zipcodesuccess" Style = "color:green">'+data+'</span>');

                        }else{
                        $("#zipcodesuccess").hide();  
                        $("[name='postcode']").after('<span id="zipcodeerror" Style = "color:red">Delivery is not available.</span>');
                        }
z                    },
                    error: function () {
                    alert("error");
                    }
            });
        }
    });

    return function (targetModule) {
        targetModule.crazyPropertyAddedHere = 'yes';
        return targetModule;
    };
});