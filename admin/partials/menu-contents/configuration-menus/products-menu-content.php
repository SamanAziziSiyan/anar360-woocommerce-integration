<?php
$api_url = 'https://api.anar360.com/api/360/products';

$awca_products = awca_get_data_from_api($api_url);


// var_dump($awca_products);
?>
<div class="stepContent" id="stepContent2">

    <h1 class='awca_plugin_titles'><?php echo esc_html__('لیست محصولات انار', 'anar-360'); ?></h1>
    <p class="awca_plugin_subTitles">
        در این مرحله محصولاتی که قراره توی وبسایت ووکامرسیت به فروش برسن رو می‌بینی :))
        این محصولات همون محصولاتی هستن که به پنل انار خودت اضافه کردی. در صورتی که مایل بودی محصولات بیشتری داخل وبسایت خودت به فروش برسونی یا بعضی از محصولات رو حذف کنی، از داخل اکانت انارت این کار رو انجام بده.
    </p>

    <?php if ($awca_products != null) : ?>
        <table class="awca_product_list_table">
            <thead>
                <tr>
                    <th>ردیف</th>
                    <th>تصویر</th>
                    <th>نام محصول</th>
                    <th>توضیحات</th>
                    <th>ویژگی‌ها</th>
                    <th>قیمت همکاری</th>
                    <th>قیمت فروش شما</th>
                    <th>اطلاعات ارسال</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($awca_products->items as $index => $item) : ?>
                    <?php $product = $item;
                    ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><img class="awca_product_images" src="<?php echo isset($item->mainImage) ? $item->mainImage : awca_default_product_image(); ?>" alt="<?php echo $product->title; ?>"></td>
                        <td class="awca_product_title"><?php echo $product->title; ?></td>
                        <td style="cursor: pointer;" onclick="awca_complete_desc(<?php echo htmlspecialchars(json_encode($product->description), ENT_QUOTES, 'UTF-8'); ?>)">
                            <?php echo awca_product_short_desc(wp_strip_all_tags($product->description)); ?>
                        </td>
                        <?php
                        ob_start();
                        ?>
                        <table>
                            <tr>
                                <th>اسم ویژگی</th>
                                <th>مقدار ویژگی</th>
                            </tr>
                            <?php if (isset($item->attributes) && is_array($item->attributes)) :
                                foreach ($item->attributes as $attribute) :
                            ?>
                                    <tr>
                                        <td><?php echo $attribute->name; ?></td>
                                        <td>
                                            <?php foreach ($attribute->values as $value) :
                                                echo htmlspecialchars($value) . '<br />';
                                            endforeach;
                                            ?>
                                        </td>
                                    </tr>
                            <?php endforeach;
                            endif;
                            ?>
                        </table>


                        <?php
                        $html = ob_get_clean();
                        $encodedHtml = json_encode($html);
                        ?>

                        <td style="cursor: pointer;" onclick="awca_complete_desc(<?php echo htmlspecialchars($encodedHtml, ENT_QUOTES, 'UTF-8'); ?>, 'ویژگی‌های محصول')">
                            مشاهده
                        </td>


                        <td class="awca_product_price"><?php echo awca_product_price_digits_seprator($product->variants[0]->priceForResell); ?> تومان</td>
                        <td class="awca_product_price"><?php echo awca_product_price_digits_seprator($product->variants[0]->price); ?> تومان</td>
                        <?php
                        ob_start();
                        ?>
                        <ul>
                            <?php
                            if (isset($item->shipments) && is_array($item->shipments)) :
                                foreach ($item->shipments as $shipment) :
                                    if (isset($shipment->type) && isset($shipment->delivery) && is_array($shipment->delivery)) :
                            ?>
                                        <li>
                                            <strong><?php echo htmlspecialchars($shipment->type); ?>:</strong>
                                            <ul>
                                                <?php
                                                foreach ($shipment->delivery as $value) :
                                                ?>
                                                    <li>
                                                        <?php
                                                        echo "روش ارسال: " . htmlspecialchars($value->deliveryType) . ", ";
                                                        echo "قیمت: " . htmlspecialchars($value->price) . ", ";
                                                        echo "تخمین زمانی: " . htmlspecialchars($value->estimatedTime);
                                                        ?>
                                                    </li>
                                                <?php
                                                endforeach;
                                                ?>
                                            </ul>
                                        </li>
                            <?php
                                    endif;
                                endforeach;
                            endif;
                            ?>
                        </ul>

                        <?php
                        $htmlshipment = ob_get_clean();

                        $encodedHtml = htmlspecialchars(json_encode($htmlshipment), ENT_QUOTES, 'UTF-8');
                        ?>
                        <td style="cursor: pointer;" onclick="awca_complete_desc(<?php echo $encodedHtml; ?>,'روش‌های ارسال محصول')">مشاهده</td>
                    </tr>
                <?php endforeach; ?>

            </tbody>

        </table>
        <div class="stepper_button_container">
            <?php
            submit_button('مرحله قبل', 'plugin_activation_button prev_stepper_btn', 'plugin_activation_button', false, array(
                'id' => 'next_stepper_btn',
                'onClick' => 'awca_move_to_step(1)'
            )); ?>

            <?php
            submit_button('مرحله بعد', 'plugin_activation_button stepper_btn', 'plugin_activation_button', false, array(
                'id' => 'next_stepper_btn',
                'onClick' => 'awca_move_to_step(3)'
            )); ?>
        </div>
    <?php else : ?>
        بازیابی اطلاعات محصولات با مشکل مواجه شد لطفا صفحه را بروزرسانی نمایید
    <?php endif; ?>
</div>



<div class="modal-wrapper" id="fullDescModal">
    <div class="modal">
        <span class="close-btn">&times;</span>
        <div class="modal-content">
            <h2 id="fullDesctitle">توضیحات کامل محصول</h2>
            <p id="fullDescContent"></p>
        </div>
    </div>
</div>