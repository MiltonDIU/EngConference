<style type="text/css">
    @font-face {
        font-family: 'title';
        font-style: normal;
        font-weight: 400;
        src: url('{{"fonts/OpenSans-Regular.ttf"}}') format('truetype');
    }
    @font-face {
        font-family: 'name';
        font-style: normal;
        font-weight: 400;
        src: url('{{"fonts/Comforter-Regular.ttf"}}') format('truetype');
    }
    @page {
        margin: 0cm 0cm;
        size: 16.70cm 22.28cm landscape;
    }
    body {
        margin-top:    0cm;
        margin-bottom: 0cm;
        margin-left:   0cm;
        margin-right:  0cm;
    }
    #watermark {
        position: fixed;
        top:   0px;
        left:     0px;
        width:    842px;
        height:   631px;
        z-index:  -1000;
        /*background-image: url("img/9th.jpg");*/
        background-image: url("img/certificate.png");
        {{--        background-image: url("{{ url('img/9th.jpg') }}");--}}
background-size: cover;
    }

    table{width: 100%}
    .container{border: 0px;width: 100%;  margin: 0 auto; padding: 23px}
    .row{float: left; width: 100%}
    .name{width: 100%; float: left; text-align: center; font-size: 40px; font-family: 'name'; color: #000000;  padding-top: 212px;}

    .title{width: 100%; float: left; text-align: center; font-size: 20px; font-family: 'title';  color: #000000; padding-top: 28px; }
</style>
<div id="watermark">
    <table class="container">
        <tr class="row">
            <td class="col-1" >
                <table style=" margin-top: -12px">
                    <tr class="row">
                        <td class="name">
                            {{ $attendance->user->name }}
                        </td>
                    </tr>
                    <tr class="row">
                        <td  class="title">
                            {{ $schedule->title }}
                        </td>
                    </tr>
                </table>
            </td>

        </tr>
    </table>
</div>
