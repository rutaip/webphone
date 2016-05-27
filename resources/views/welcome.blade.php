@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-primary">
                <div class="panel-heading">Rutaip WebRTC phone</div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-1">
                            <div class="page-header">
                                <h3>Un concepto nuevo en telecomunicaciones.</h3>
                            </div>
                            <p class="text-justify">
                            Por medio de la tecnología WebRTC Rutaip pone al alcance un sofpthone Plug&Play para cualquier tipo de IP PBX.
                            <br>
                            <br>

                            Si desea conocer la gama de posibilidades que puede extender a sus clientes le invitamos a ponerse en contacto con nososotros.

                            </p>
                            <p>{!! Html::link('http://www.rutaip.net', 'Rutaip') !!} info@rutaip.net</p>

                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-4">{{ Html::image('assets/img/webrtc-logo.png', 'Webrtc', array('class' => 'img-responsive')) }}</div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
