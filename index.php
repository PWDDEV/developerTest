<?php

class InfoBlocks {
    public $food = array (
        array (
            'product_name' => "Apple",
            'in_stock' => 22,
            'sold' => 17
        ),
        array (
            'product_name' => "Strawberry",
            'in_stock' => 15,
            'sold' => 13
        ),
        array (
            'product_name' => "Banana",
            'in_stock' => 130,
            'sold' => 35
        ),
        array (
            'product_name' => "Kiwi",
            'in_stock' => 150,
            'sold' => 17
        ),
        array (
            'product_name' => "Bomb",
            'in_stock' => 1600,
            'sold' => 17
        ),
    );

    public function test_method ($cached_cell, $sort_type, $filter_by_keyword, $field_1_optional, $field_2_optional) {
        // caching
        apc_store($cached_cell, $this->food);
        $cached_cell = apc_fetch($cached_cell);
        // END caching

        // sorting
        switch ($sort_type) {
            case "a": // sorting by index
                asort($cached_cell);
                break;
            case "ar": // reverse sorting by index
                arsort($cached_cell);
                break;
            case "k": // sorting by key
                ksort($cached_cell);
                break;
            case "kr": // reverse sorting by key
                krsort($cached_cell);
                break;
            default:
                ;
        }
        // END sorting

        // filtering
        if ($filter_by_keyword) {
            $m_cell = array();
            $key = $filter_by_keyword;

            $m_cell_after_one_key = array_filter(
                $cached_cell,
                function ($var) use ($key) {
                    foreach ($var as $value) {
                        if ($value == $key) {
                            return $value;
                        }
                    }
                }
            );

            $m_cell = array_merge($m_cell, $m_cell_after_one_key);

            $cached_cell = $m_cell;
        }
        // END filtering

        // fields
        if ($field_1_optional || $field_2_optional) {
            $cached_cell = array_column($cached_cell, $field_1_optional, $field_2_optional);
        } else {
            $this->render_table_from_cached_array($cached_cell);
        }
        // END fields

        return $cached_cell;
    }

    public function render_table_from_cached_array ($cached_cell) {
        echo '
            <style>
                table {
                    width: 300px;
                }

                th {
                    text-align: left;
                }
            </style>
            <table>
                <tr>
                    <th>Product name</th>
                    <th>In stock</th>
                    <th>Sold</th>
                </tr>' . $this->render_rows_from_array($cached_cell) . '
            </table>
        ';
    }

    public function render_rows_from_array ($cached_cell) {
        $code_block = "";
        foreach ($cached_cell as $row) {
            $code_block .= "\n\t\t\t\t<tr>";
            foreach ($row as $cell) {
                $code_block .= "\n\t\t\t\t\t<td>" . $cell . "</td>";
            }
            $code_block .= "\n\t\t\t\t</tr>";
        }
        return $code_block;
    }
}

// RENDER PAGE
$page = new InfoBlocks();
$page->test_method("food", "kr", "17", "", "");
// END RENDER PAGE