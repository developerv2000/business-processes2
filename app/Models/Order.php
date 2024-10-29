<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

        /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    /**
     * Provides the default table columns along with their properties.
     *
     * These columns are typically used to display data in tables,
     * such as on index and trash pages, and are iterated over in a loop.
     *
     * @return array
     */
    public static function getDefaultTableColumnsForUser($user): array
    {
        $order = 1;
        $columns = array();

        array_push(
            $columns,
            ['name' => 'ID', 'order' => $order++, 'width' => 70, 'visible' => 1],
            ['name' => 'Receive date', 'order' => $order++, 'width' => 146, 'visible' => 1],
            ['name' => 'PO date', 'order' => $order++, 'width' => 84, 'visible' => 1],
            ['name' => 'PO №', 'order' => $order++, 'width' => 144, 'visible' => 1],
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Market', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'Brand Eng', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'Brand Rus', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'MAH', 'order' => $order++, 'width' => 102, 'visible' => 1],
            ['name' => 'Quantity', 'order' => $order++, 'width' => 158, 'visible' => 1],
            ['name' => 'Price', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Currency', 'order' => $order++, 'width' => 92, 'visible' => 1],
            ['name' => 'Currency', 'order' => $order++, 'width' => 92, 'visible' => 1],
            ['name' => 'Sum', 'order' => $order++, 'width' => 112, 'visible' => 1],
            ['name' => 'Readness date', 'order' => $order++, 'width' => 114, 'visible' => 1],
            ['name' => 'Mfg lead time', 'order' => $order++, 'width' => 118, 'visible' => 1],
            ['name' => 'Expected dispatch date', 'order' => $order++, 'width' => 118, 'visible' => 1],
            ['name' => 'Comments', 'order' => $order++, 'width' => 132, 'visible' => 1],
            ['name' => 'Last comment', 'order' => $order++, 'width' => 240, 'visible' => 1],
            ['name' => 'Comments date', 'order' => $order++, 'width' => 116, 'visible' => 1],
        );

        return $columns;
    }
}
