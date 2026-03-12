<section id="strategics" class="wow fadeInUp">

  <div class="container">
    <div class="section-header">
      <h2>Organizer</h2>
    </div>

    <div class="row no-gutters supporters-wrap clearfix">

      @foreach($strategics as $sponsor)

        <div class="col-md-4 col-6">
          <div class="supporter-logo">
            <img src="{{ $sponsor->logo!=null?$sponsor->logo->getUrl():"" }}" class="img-fluid" alt="{{ $sponsor->name }}">
          </div>
        </div>
      @endforeach
    </div>

  </div>

</section>
