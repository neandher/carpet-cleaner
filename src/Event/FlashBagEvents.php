<?php

namespace App\Event;

class FlashBagEvents
{
    const MESSAGE_TYPE_SUCCESS = 'success';
    const MESSAGE_TYPE_ERROR = 'danger';

    const MESSAGE_SUCCESS_INSERTED = 'The record has been created successfully';
    const MESSAGE_ERROR_INSERTED = 'An error has occurred';

    const MESSAGE_SUCCESS_UPDATED = 'The record has been updated successfully';
    const MESSAGE_ERROR_UPDATED = 'An error has occurred';

    const MESSAGE_SUCCESS_DELETED = 'The record has been deleted successfully';
    const MESSAGE_ERROR_DELETED = 'An error has occurred';
}