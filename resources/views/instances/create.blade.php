@extends('template')

@section('page')
    Partners
@stop

@section('content')

    {!! Form::open(array('url' => 'instances')) !!}

    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered">
                <caption>Instance Details</caption>
                <thead>
                <tr class="active">
                    <th>Field</th>
                    <th>Content</th>
                </tr> </thead>
                <tbody>
                <tr>
                    <th scope=row>{!! Form::label('name', 'Instance Name') !!}
                        <a href="#" data-toggle="popover" data-trigger="focus" title="Project Name" data-content="The name of the project">
                            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                        </a>
                    </th>
                    <td>{!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Rutaip WebRTC']) !!}</td>

                </tr>
                <tr>
                    <th scope=row>{!! Form::label('websocket_proxy_url', 'Websocket proxy URL') !!}</th>
                    <td>{!! Form::text('websocket_proxy_url', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'wss://phone.rutaip.net:8089/asterisk/ws']) !!}</td>
                </tr>
                <tr>
                    <th scope=row>{!! Form::label('outbound_proxy', 'Outbound Proxy') !!}</th>
                    <td>{!! Form::text('outbound_proxy', null, ['class' => 'form-control', 'placeholder' => 'udp://pbx.rutaip.net:5060']) !!}</td>
                </tr>
                <tr>
                    <th scope=row>{!! Form::label('ice_servers', 'Ice Servers') !!}</th>
                    <td>{!! Form::text('ice_servers', null, ['class' => 'form-control', 'placeholder' => '[{ url:"stun.ekiga.net"}]']) !!}</td>
                </tr>
                <tr>
                    <th scope=row>{!! Form::label('rtcweb_breaker', 'WebRTC Breaker') !!}</th>
                    <td>{!! Form::select('rtcweb_breaker', ['true' => 'True', 'false' => 'False'], old('rtcweb_breaker'),['class' => 'form-control', 'required' => 'required']) !!}</td>
                </tr>
                <tr>
                    <th scope=row>{!! Form::label('bandwidth', 'Bandwidth') !!}</th>
                    <td>{!! Form::text('bandwidth', null, ['class' => 'form-control', 'placeholder' => '{ audio:64, video:512 }']) !!}</td>
                </tr>
                <tr>
                    <th scope=row>{!! Form::label('video_enable', 'Enable Video') !!}</th>
                    <td>{!! Form::select('video_enable', ['false' => 'False', 'true' => 'True'], old('video_enable'),['class' => 'form-control', 'required' => 'required']) !!}</td>
                </tr>
                <tr>
                    <th>{!! Form::label('video_size', 'Video Size') !!}</th>
                    <td>{!! Form::text('video_size', null, ['class' => 'form-control', 'placeholder' => '{ minWidth: 640, minHeight:480, maxWidth: 640, maxHeight:480 }']) !!}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">

        </div>
        <div class="col-md-4">

            {!! Form::submit('Submit', ['class' => 'btn btn-primary form-control']) !!}
        </div>


    </div>




    {!! Form::close() !!}

    <br>

    @include('errors.form')


@stop