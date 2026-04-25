<section id="about">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <h2>About The Event</h2>
        <p style="text-align:justify">{{ $settings['about_description'] ?? '' }}</p>
      </div>
      <div class="col-lg-6">
        <h2>Where & When</h2>
        <p>{!! $settings['about_where'] ?? '' !!}</p>
        <p>{!! $settings['about_when'] ?? '' !!}</p>
      </div>
    </div>
  </div>
</section>
