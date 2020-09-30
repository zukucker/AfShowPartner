//$.subscribe('plugin/swCookieConsentManager/onBuildCookiePreferences', function(event, plugin, preferences){
    //console.error("Do something like removing a cookie or displaying some warning regrading possible issues!");
//});

// let hasPartnerTracking = $.getCookiePreference("partnertracking");
// console.error(hasPartnerTracking);
//
// if(hasPartnerTracking){
//     console.error("cookie registred and activated");
// }


if($.getCookiePreference('allow_local_storage')){
    window.onload = init;

    function init(){
        var link = window.location.href;
        if(link.includes("?sPartner")){
            var host = window.location.hostname;
            var proto = window.location.protocol;
            var partnerWith = link.replace(host, '');
            var partner = partnerWith.replace(proto, '');
            var partnerlink = (partner.substring(2,999));
            /* the end name */
            var name = partnerlink.replace("/?sPartner=", '');

            sessionStorage.setItem('partner', name);
        };
    }
    let partner = sessionStorage.getItem('partner');
}
