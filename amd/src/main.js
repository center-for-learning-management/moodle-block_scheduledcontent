define(
    ['jquery', 'core/ajax', 'core/notification', 'core/str', 'core/url', 'core/modal_factory', 'core/modal_events'],
    function($, ajax, notification, str, url, ModalFactory, ModalEvents) {
    return {
        debug: 0,
        modal: function(id) {
            if (this.debug > 0) console.log('block_scheduledcontent/main:modal(id)', id);
            ajax.call([{
                methodname: 'block_scheduledcontent_modal',
                args: { 'id': id, },
                done: function(result) {
                    if (result != '' && result != null) {
                        ModalFactory.create({
                            type: ModalFactory.types.OK,
                            title: '',
                            body: result,
                        }).then(function(modal) {
                            modal.show();
                        });
                    }
                },
                fail: notification.exception
            }]);
        },
    };
});
