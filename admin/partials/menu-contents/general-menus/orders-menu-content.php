<div class="wrapper">

<?php

echo "<h1 class='awca_plugin_titles'>" . esc_html__('سفارشات انار', 'anar-360') . "</h1>";

$api_url = 'https://api.anar360.com/api/360/orders';

awca_get_data_from_api($api_url);

?>
</div>