<?php
if (!function_exists('get_the_acf_content')) {
    function get_the_acf_content($page = null, $stripped = false)
    {
        $page = $page ? $page : get_the_ID();
        $blocks = [
            'raw_code' => 'raw-code.php',
        ];
        $result = '';

        if (have_rows('site_flexible_content', $page)) :
            while (have_rows('site_flexible_content', $page)) : the_row();
                $key = get_row_layout();
                if (array_key_exists($key, $blocks)) {
                    ob_start();
                    require locate_template("flexible-content/{$blocks[$key]}");
                    $result .= ob_get_clean();
                }
            endwhile;
        endif;

        return $stripped ? strip_tags($result) : $result;
    }
}

if (!function_exists('the_acf_content')) {
    function the_acf_content($page = null, $stripped = false)
    {
        echo get_the_acf_content($page, $stripped);
    }
}
