window.onload = init;

function init() {
    var link = window.location.href;
    var host = window.location.hostname;
    var proto = window.location.protocol;
    var partnerWith = link.replace(host, '')
    var partner = partnerWith.replace(proto, '');
    var partnerlink = (partner.substring(2,999));

    console.error(partnerlink);
    localStorage.setItem('partnerLink', JSON.stringify(partnerlink))

    console.error(localStorage);
}
