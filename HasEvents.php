trait HasEvents
{
    protected static $events_to_observe = [
        'creating' => 'creatingItem',
        'created' => 'createdItem',
        'deleting' => 'deletingItem',
        'deleted' => 'deletedItem',
        'updated' => 'updatedItem',
        'updating' => 'updatingItem',
        'saving' => 'savingItem',
        'saved' => 'savedItem',
        'retrieved' => 'retrievedItem',
    ];

    protected static function boot()
    {
        parent::boot();
        foreach (static::getModelEvents() as $event => $action) {
            if (method_exists(static::class, $action)) {
                static::$event(function ($item) use ($event, $action) {
                    $item->$action($item);
                });
            }
        }
    }

    protected static function getModelEvents()
    {
        if (isset(static::$events_to_observe)) {
            //if a model needs fewer events available to SavesItem, define in that model's $modelEvents array.
            return static::$events_to_observe;
        }
        return [];
    }
}
