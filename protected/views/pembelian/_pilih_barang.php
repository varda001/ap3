<div id="pilih-barang" class="medium-6 large-5 columns">
   <div class="panel">
      <h5>Pilih Barang: <a href="#" id="tambah-barang-baru" class="button tiny bigfont right" accesskey="g">Tambah baran<span class="ak">g</span></a></h5>

      <?php echo CHtml::label('<span class="ak">1</span> Barcode', 'barcode'); ?>
      <div class="row collapse">
         <div class="medium-10 columns">
            <?php echo CHtml::dropDownList('barcode', '', $barangBarcode, array('accesskey' => '1', 'id' => 'barcode-pilih')); ?>
         </div>
         <div class="medium-2 columns">
            <a href="#" id="pilih-barcode" class="button postfix tombol-pilih" accesskey="2"><span class="ak">2</span> Pilih</a>
         </div>
      </div>
      <?php echo CHtml::label('<span class="ak">3</span> Nama', 'nama'); ?>
      <div class="row collapse">
         <div class="medium-10 columns">
            <?php echo CHtml::dropDownList('nama', '', $barangNama, array('accesskey' => '3', 'id' => 'nama-pilih')); ?>
         </div>
         <div class="medium-2 columns">
            <a href="#" id="pilih-nama" class="button postfix tombol-pilih" accesskey="4" ><span class="ak">4</span> Pilih</a>
         </div>
      </div>
   </div>
</div>
<script>
   $("#barcode-pilih").keyup(function (e) {
      if (e.keyCode === 13) {
         $("#pilih-barcode").click();
      }
   });

   $("#nama-pilih").keyup(function (e) {
      if (e.keyCode === 13) {
         $("#pilih-nama").click();
      }
   });

   $(".tombol-pilih").click(function () {
      var barangId = $(this).parent('div').parent('div').find('select').val();
      var datakirim = {
         'barangId': barangId
      };
      var dataurl = "<?php echo $this->createUrl('getbarang') ?>";

      $.ajax({
         data: datakirim,
         url: dataurl,
         type: "POST",
         dataType: "json",
         success: updateFormDetail
      });
   });

   /**
    * Update nilai-nilai pada form input pembelian barang
    * @param json Informasi barang
    * @returns {mixed} Menampilkan form input pembelian barang dan mengisi field yang diperlukan
    */
   function updateFormDetail(info) {
      $("#barang-info").html(info['nama'] + ' <small>' + info['barcode'] + '</small>');
      $("#barang-id").val(info['barangId']);
      $("#label-harga-beli").text('Harga Beli (' + info['labelHargaBeli'] + ')');
      $("#harga-beli").val(info['hargaBeli']);
      $("#label-harga-jual").text('Harga Jual (' + info['labelHargaJual'] + ')');
      $("#harga-jual").val(info['hargaJual']);
      $("#label-rrp").text('RRP (' + info['labelRrp'] + ')');
      $("#rrp").val(info['rrp']);
      $("#satuan").text(info['satuan']);
      $("#qty").val('');
      $("#subtotal").val('');
      $("#input-pemb-detail").slideDown(500);
      $("#qty").focus();
   }

   $(document).on("click", "#hitung-harga", function () {
      hitungHargaBarang();
   });

   /**
    * Menghitung harga beli dan harga jual satuan
    * @returns {mixed} Mengubah value di input harga beli dan harga jual
    * */
   function hitungHargaBarang() {
      var hargaBeli = 0;
      var hargaJual = 0;
      var subTotal = parseInt($("#subtotal").val());
      var ppn = parseInt($("#ppn").val()) || 0;
      var qty = parseInt($("#qty").val()) || 0;
      var profitPersen = parseInt($("#profit").val()) || 0;
      var diskonPersen = parseFloat($("#diskonp").val()) || 0;
      var diskonRupiah = parseInt($("#diskonr").val()) || 0;
      //console.log('st: ' + subTotal + ', ppn: ' + ppn, ', qty: ' + qty + ', prof: ' + profitPersen + ', dp: ' + diskonPersen + ', dr: ' + diskonRupiah);
      if (subTotal > 0) {
         hargaBeli = (subTotal / qty);

         // Hitung diskon dulu
         hargaBeli = hargaBeli - (hargaBeli / 100 * diskonPersen) - diskonRupiah;

         // Baru kemudian hitung PPN
         hargaBeli = hargaBeli + (hargaBeli / 100 * ppn);
         hargaJual = hargaBeli + (hargaBeli / 100 * profitPersen);
         $("#harga-beli").val(hargaBeli);
         $("#harga-jual").val(hargaJual);
      }
   }

   $("#tambah-barang-baru").click(function () {
      $("#input-barang-baru").slideDown(500);
      $("#Barang_barcode").focus();
   });

   function bersihkanInputBarangBaru() {
      $("#input-barang-baru h5").html("Tambah Barang Baru")
      $("#Barang_barcode").val('');
      $("#Barang_nama").val('');
      $("#Barang_kategori_id").val('');
      $("#Barang_satuan_id").val('');
      $("#Barang_rak_id").val('');
   }
</script>
<div id="input-barang-baru" class="medium-6 large-7 columns" style="display: none">
   <?php
   $formInputBaru = $this->beginWidget('CActiveForm', array(
       'id' => 'barang-baru-form',
       'action' => $this->createUrl('tambahbarangbaru', array('id' => $pembelianModel->id)),
       'enableAjaxValidation' => false,
       // 'htmlOptions' => array("onsubmit" => "return false;")
   ));
   ?>
   <div class="panel">
      <h5>Tambah Barang Baru</h5>
      <div class="row">
         <div class="medium-5 large-4 columns">        
            <?php echo $formInputBaru->labelEx($barang, 'barcode'); ?>
            <?php echo $formInputBaru->textField($barang, 'barcode', array('size' => 45, 'maxlength' => 45, 'autocomplete' => 'off')); ?>
            <?php echo $formInputBaru->error($barang, 'barcode', array('class' => 'error')); ?>
         </div>
         <div class="medium-7 large-8 columns">        
            <?php echo $formInputBaru->labelEx($barang, 'nama'); ?>
            <?php echo $formInputBaru->textField($barang, 'nama', array('size' => 45, 'maxlength' => 45, 'autocomplete' => 'off')); ?>
            <?php echo $formInputBaru->error($barang, 'nama', array('class' => 'error')); ?>
         </div>
      </div>
      <div class="row">
         <div class="medium-6 large-4 columns">
            <?php
            echo $formInputBaru->labelEx($barang, 'kategori_id');
            echo $formInputBaru->dropDownList($barang, 'kategori_id', CHtml::listData(KategoriBarang::model()->findAll(array('order' => 'nama')), 'id', 'nama'), array(
                'empty' => 'Pilih satu..'
            ));
            echo $formInputBaru->error($barang, 'kategori_id', array('class' => 'error'));
            ?>
         </div>
         <div class="medium-6 large-4 columns">            
            <?php
            echo $formInputBaru->labelEx($barang, 'satuan_id');
            echo $formInputBaru->dropDownList($barang, 'satuan_id', CHtml::listData(SatuanBarang::model()->findAll(array('order' => 'nama')), 'id', 'nama'), array(
                'empty' => 'Pilih satu..'
            ));
            echo $formInputBaru->error($barang, 'satuan_id', array('class' => 'error'));
            ?>
         </div>
         <div class="medium-6 large-4 columns">            
            <?php
            echo $formInputBaru->labelEx($barang, 'rak_id');
            echo $formInputBaru->dropDownList($barang, 'rak_id', CHtml::listData(RakBarang::model()->findAll(array('order' => 'nama')), 'id', 'nama'), array(
                'empty' => 'Pilih satu..'
            ));
            echo $formInputBaru->error($barang, 'rak_id', array('class' => 'error'));
            ?>
         </div>  
      </div>
      <div class="row">
         <div class="span-12 columns">
            <?php
            echo CHtml::ajaxLink('Simpan (Alt+m)', $this->createUrl('tambahbarangbaru', array(
                        'id' => $pembelianModel->id,)), array(
                'type' => 'POST',
                'success' => "function (data) {
                                    if (data.sukses){
                                       $('#input-barang-baru').slideUp(500);
                                       updateFormDetail(data);
                                       bersihkanInputBarangBaru();
                                    } else {
                                       $('#input-barang-baru h5').html(data.msg);
                                    }
                              }"
                    ), array(
                'id' => 'tombol-tambah-barang-baru',
                'class' => 'tiny bigfont button',
                'accesskey' => 'm'
            ));
            ?>
            <a class="tiny bigfont button" id="tombol-batal" href="#" accesskey="b" onclick="$('#input-barang-baru').slideUp(500);
                  bersihkanInputBarangBaru();"><span class="ak">B</span>atal
            </a>
         </div>
      </div>
   </div>
   <?php $this->endWidget(); ?>
   <script>
      $("#barang-baru-form").submit(function(){
         return false;
      });
      
      $("#Barang_barcode").keyup(function (e) {
         if (e.keyCode === 13) {
            $("#Barang_nama").focus();
            $("#Barang_nama").select();
         }
      });
   </script>
</div>
<div id="input-pemb-detail" class="medium-6 large-7 columns" style="display: none">
   <?php
   $form = $this->beginWidget('CActiveForm', array(
       'id' => 'pembelian-form',
       'action' => $this->createUrl('tambahbarang', array('id' => $pembelianModel->id)),
       'enableAjaxValidation' => false,
   ));
   ?>
   <input type="hidden" name="barang-id" id="barang-id" value="" />
   <input type="hidden" name="input-detail" value="1" />
   <div class="panel">
      <h5><span id="barang-info"></span></h5>
      <div class="row">
         <div class="medium-6 large-4 columns">
            <?php echo CHtml::label('<u><b>J</b></u>umlah yang dibeli', 'qty') ?>
            <div class="row collapse">
               <div class="small-9 columns">
                  <?php echo CHtml::textField('qty', '', array('accesskey' => 'j', 'autocomplete' => 'off')); ?>
               </div>
               <div class="small-3 columns">
                  <span class="postfix"><b><span id="satuan"></span></b></span>
               </div>
            </div>
            <?php echo CHtml::label('Sub Total', 'subtotal') ?>
            <div class="row collapse">
               <div class="small-3 columns">
                  <span class="prefix"><b>Rp.</b></span>
               </div>
               <div class="small-9 columns">
                  <?php echo CHtml::textField('subtotal', ''); ?>
               </div>
            </div>
         </div>
         <div class="medium-6 large-4 columns">
            <?php echo CHtml::label('PPN', 'ppn') ?>
            <div class="row collapse">
               <div class="small-9 columns">
                  <?php echo CHtml::textField('ppn', ''); ?>
               </div>
               <div class="small-3 columns">
                  <span class="postfix"><b>%</b></span>
               </div>
            </div>
            <?php echo CHtml::label('Profit', 'profit') ?>
            <div class="row collapse">
               <div class="small-9 columns">
                  <?php echo CHtml::textField('profit', ''); ?>
               </div>
               <div class="small-3 columns">
                  <span class="postfix"><b>%</b></span>
               </div>
            </div>
         </div>
         <div class="medium-6 large-4 columns">
            <?php echo CHtml::label('Diskon', 'diskonp') ?>
            <div class="row collapse">
               <div class="small-9 columns">
                  <?php echo CHtml::textField('diskonp', ''); ?>
               </div>
               <div class="small-3 columns">
                  <span class="postfix"><b>%</b></span>
               </div>
            </div>
         </div>
         <div class="medium-6 large-4 columns">
            <?php echo CHtml::label('Diskon', 'diskonr') ?>
            <div class="row collapse">
               <div class="small-3 columns">
                  <span class="prefix"><b>Rp.</b></span>
               </div>
               <div class="small-9 columns">
                  <?php echo CHtml::textField('diskonr', ''); ?>
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="small-12 medium-6 medium-centered columns">
            <a href="#" class="button tiny bigfont small-12 columns" accesskey="6" id="hitung-harga">Hitung Harga (Alt+6)</a>
         </div>
      </div>
      <div class="row">
         <div class="medium-6 large-3 columns">
            <?php echo CHtml::label('Harga Beli', 'hargabeli', array('id' => 'label-harga-beli')) ?>
            <?php echo CHtml::textField('hargabeli', '', array('id' => 'harga-beli', 'autocomplete' => 'off')); ?>
         </div>
         <div class="medium-6 large-3 columns">
            <?php echo CHtml::label('Harga Jual', 'hargajual', array('id' => 'label-harga-jual')) ?>
            <?php echo CHtml::textField('hargajual', '', array('id' => 'harga-jual', 'autocomplete' => 'off')); ?>
         </div>
         <div class="medium-6 large-3 columns">
            <?php echo CHtml::label('RRP', 'rrp', array('id' => 'label-rrp')) ?>
            <?php echo CHtml::textField('rrp', '', array('id' => 'rrp', 'autocomplete' => 'off')); ?>
         </div>
         <div class="medium-6 large-3 columns">
            <?php echo CHtml::label('Tanggal Expire', 'tanggal_kadaluwarsa') ?>
            <?php echo CHtml::textField('tanggal_kadaluwarsa', '', array('placeholder' => 'yyyy-mm-dd')); ?>
         </div>
      </div>
      <div class="row">
         <div class="span-12 columns">
            <?php
            echo CHtml::ajaxSubmitButton('Tambah (Alt+a)', $this->createUrl('tambahbarang', array(
                        'id' => $pembelianModel->id,)), array(
                'type' => 'POST',
                'success' => "function () {
                                        $.fn.yiiGridView.update('pembelian-detail-grid');
                                        updateTotal();
                                        $('barcode-pilih').focus();
                                        $('#input-pemb-detail').slideUp(500);
                                    }"
                    ), array(
                'id' => 'tombol-tambah',
                'class' => 'tiny bigfont button',
                'accesskey' => 'a'
            ));
            ?>
            <a class="tiny bigfont button" id="tombol-batal" href="#" accesskey="l" onclick="$('#input-pemb-detail').slideUp(500);">Bata<span class="ak">l</span></a>
         </div>
      </div>
   </div>
   <?php $this->endWidget(); ?>
</div>