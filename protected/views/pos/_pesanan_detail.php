<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/vendor/jquery.poshytip.js',
        CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/vendor/jquery-editable-poshytip.min.js',
        CClientScript::POS_HEAD);
Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl . '/css/jquery-editable.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl . '/css/responsive-tables.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/responsive-tables.js',
        CClientScript::POS_HEAD);
Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl . '/css/animate.css');

$this->widget('BGridView',
        [
    'id'            => 'pesanan-detail-grid',
    'dataProvider'  => $modelDetail->search(),
    //'filter' => $modelDetail,
    //'summaryText' => 'Poin struk ini: ' . $pesanan->getCurPoin() . ' | Poin sebelumnya: ' . $pesanan->getTotalPoinPeriodeBerjalan() . ' | {start}-{end} dari {count}',
    'itemsCssClass' => 'tabel-index responsive',
    'template'      => '{items}{pager}{summary}',
    'enableSorting' => false,
    'columns'       => [
        [
            'header'            => '#',
            'type'              => 'raw',
            'value'             => '$this->grid->dataProvider->pagination->itemCount - $this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize - ($row)',
            'headerHtmlOptions' => ['class' => 'rata-kanan'],
            'htmlOptions'       => ['class' => 'rata-kanan'],
        ],
        [
            'name'        => 'barcode',
            'value'       => '$data->barang->barcode',
            'htmlOptions' => ['class' => 'barcode'],
        ],
        [
            'name'  => 'namaBarang',
            'type'  => 'raw',
            'value' => [$this, 'renderNamaBarang'],
        ],
        /*
          array(
          'name' => 'namaBarang',
          'value' => '$data->barang->nama',
          'headerHtmlOptions' => array('class' => 'show-for-large-up'),
          'htmlOptions' => array('class' => 'show-for-large-up'),
          ),
         *
         */
        [
            'header'            => 'Harga',
            'headerHtmlOptions' => ['class' => 'rata-kanan show-for-large-up'],
            'htmlOptions'       => ['class' => 'rata-kanan show-for-large-up'],
            'value'             => function($data) {
                return rtrim(rtrim(number_format($data->harga_jual + $data->diskon, 2, ',', '.'), '0'), ',');
            }
        ],
        [
            'name'              => 'diskon',
            'header'            => 'Diskon',
            'headerHtmlOptions' => ['class' => 'rata-kanan show-for-large-up'],
            'htmlOptions'       => ['class' => 'rata-kanan show-for-large-up'],
            'value'             => function($data) {
                return rtrim(rtrim(number_format($data->diskon, 2, ',', '.'), '0'), ',');
            }
        ],
        /*
          array(
          'name' => 'harga_jual',
          'header' => 'Net',
          'headerHtmlOptions' => array('class' => 'rata-kanan'),
          'htmlOptions' => array('class' => 'rata-kanan'),
          'value' => function($data) {
          return rtrim(rtrim(number_format($data->harga_jual, 2, ',', '.'), '0'), ',');
          }
          ),
         *
         */
        [
            'name'              => 'harga_jual',
            'header'            => 'Ne<span class="ak">t</span>',
            'type'              => 'raw',
            //'value'             => [$this, 'renderHargaLinkEditable'],
            'headerHtmlOptions' => ['class' => 'rata-kanan'],
            'htmlOptions'       => ['class' => 'rata-kanan'],
        ],
        [
            'name'              => 'qty',
            'header'            => '<span class="ak">Q</span>ty',
            'type'              => 'raw',
            //'value'             => [$this, 'renderQtyLinkEditable'],
            'headerHtmlOptions' => ['style' => 'width:75px;', 'class' => 'rata-kanan'],
            'htmlOptions'       => ['class' => 'rata-kanan'],
        ],
        /*
          array(
          'type' => 'raw',
          'value' => '"<span class=\"info label\">".$data->barang->satuan->nama."</label>"',
          'htmlOptions' => array('style' => 'padding-left:0'),
          ),
         */
        [
            'name'              => 'subTotal',
            'header'            => 'Total',
            'value'             => '$data->total',
            'headerHtmlOptions' => ['class' => 'rata-kanan'],
            'htmlOptions'       => ['class' => 'rata-kanan'],
            'filter'            => false
        ],
    ],
]);
//echo $pesanan->getCurPoin();
?>
<script>

    function enableEditable() {
        $(".editable-qty").editable({
            mode: "inline",
            inputclass: "input-editable-qty",
            success: function (response, newValue) {
                if (response.sukses) {
                    $("#tombol-admin-mode").removeClass('geleng');
                    $("#tombol-admin-mode").removeClass('alert');
                    $.fn.yiiGridView.update("pesanan-detail-grid");
                    updateTotal();
                }
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    $.gritter.add({
                        title: 'Error 500',
                        text: 'Hapus detail harus dengan otorisasi Admin',
                        time: 3000,
                    });
                    $("#tombol-admin-mode").addClass('geleng');
                    $("#tombol-admin-mode").addClass('alert');
                }
            }
        });
        $('.editable-qty').on('shown', function (e, editable) {
            setTimeout(function () {
                editable.input.$input.select();
            }, 0);
<?php /* Menambahkan selector agar width bisa diatur */ ?>
            $(".input-editable-qty").parent('.editable-input').addClass('input-editable-qty-p');
        });
        $('.editable-qty').on('hidden', function (e, reason) {
            // focus on input barcode
            $("#scan").focus();
        });

        $(".editable-harga").editable({
            mode: "inline",
            inputclass: "input-editable-harga",
            success: function (response, newValue) {
                if (response.sukses) {
                    $.fn.yiiGridView.update("pesanan-detail-grid");
                    updateTotal();
                }
            }
        });
        $('.editable-harga').on('shown', function (e, editable) {
            setTimeout(function () {
                editable.input.$input.select();
            }, 0);
<?php /* Menambahkan selector agar width bisa diatur */ ?>
            $(".input-editable-harga").parent('.editable-input').addClass('input-editable-harga-p');
        });
        $('.editable-harga').on('hidden', function (e, reason) {
            // focus on input barcode
            $("#scan").focus();
        });
    }

    $(document).on('keydown', ".editable-input input.input-editable-qty", function (event) {
        // console.log(event.which);
        if (event.which === 40) {
            console.log('next');
            $(this).closest('tr').next().find('.editable-qty').editable('show');
        } else if (event.which === 38) {
            console.log('prev');
            $(this).closest('tr').prev().find('.editable-qty').editable('show');
        }
    });

    $(document).on('keydown', ".editable-input input.input-editable-harga", function (event) {
        // console.log(event.which);
        if (event.which === 40) {
            console.log('next');
            $(this).closest('tr').next().find('.editable-harga').editable('show');
        } else if (event.which === 38) {
            console.log('prev');
            $(this).closest('tr').prev().find('.editable-harga').editable('show');
        }
    });

    $(function () {
        enableEditable();
    });

    $(document).ajaxComplete(function () {
        enableEditable();
    });
</script>
