<section id="supporters" class="wow fadeInUp section-with-bg">
  <div class="container">
    <div class="section-header text-center">
      <h2>Partners</h2>
    </div>

    <div class="row justify-content-center supporters-wrap clearfix" style="margin:0; padding:0;">
      @foreach($sponsors as $sponsor)
        <div class="col-md-3 col-6 d-flex justify-content-center align-items-center mb-4" style="padding:0;">
          <div class="supporter-logo" style="text-align:center;">
            <img src="{{ $sponsor->logo!=null ? $sponsor->logo->getUrl() : '' }}"
                 alt="{{ $sponsor->name }}"
                 class="img-fluid"
                 style=" object-fit:contain; border:none;">
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<style>
#supporters .supporters-wrap {
  border-top: 0px solid #e0e5fa;
  border-left: 0px solid #e0e5fa;
}

#supporters .supporter-logo {
  border: 1px solid #e0e5fa;
  overflow: hidden; /* ensures image doesn't spill outside */
  display: flex;
  justify-content: center;
  align-items: center;
  transition: transform 0.3s ease; /* smooth hover */
    width: 100%;
}

#supporters .supporter-logo img {
  object-fit: contain;
  transition: transform 0.3s ease; /* smooth zoom */
}

/* Optional: subtle zoom on hover */
#supporters .supporter-logo:hover img {
  transform: scale(1.05); /* small zoom */
}
</style>
