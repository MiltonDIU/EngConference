<section id="strategics" class="wow fadeInUp">
  <div class="container">
    <div class="section-header text-center">
      <h2>Organizers</h2>
    </div>

    <div class="row justify-content-center supporters-wrap clearfix" style="margin:0; padding:0;">
      @foreach($strategics as $sponsor)
        <div class="col-md-4 col-6 d-flex justify-content-center align-items-center mb-4" style="padding:0;">
          <div class="supporter-logo" style="text-align:center;">
            <img src="{{ $sponsor->logo!=null?$sponsor->logo->getUrl():"" }}"
                 alt="{{ $sponsor->name }}"
                 class="img-fluid"
                 style="max-height:120px; object-fit:contain; border:none;">
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<style>
#strategics .supporters-wrap {
  border-top: 0px solid #e0e5fa;
  border-left: 0px solid #e0e5fa;

}
    #strategics .supporter-logo{ border:1px solid #e0e5fa;}
</style>
