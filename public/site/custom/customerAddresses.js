$(document).ready(function () {
    customerAddressInit();
});

function customerAddressInit() {
    var index = 0;
    var $collectionHolder = $('#customerAddresses');
    $collectionHolder.data('index', index);
    if ($collectionHolder.find('div').length === 0) {
        addCustomerAddressForm($collectionHolder);
    }
}

function addCustomerAddressForm($collectionHolder) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var newForm = prototype.replace(/__name__/g, index);
    $('#customerAddresses').append(newForm);
}
