//$.subscribe('plugin/swCookieConsentManager/onBuildCookiePreferences', function(event, plugin, preferences){
    //console.error("Do something like removing a cookie or displaying some warning regrading possible issues!");
//});

let hasPartnerTracking = $.getCookiePreference("partnertracking");
console.error(hasPartnerTracking);

if(hasPartnerTracking){
    alert("Cookie aktiv");
}
