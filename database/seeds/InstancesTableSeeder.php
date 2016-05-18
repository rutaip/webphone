<?php

use Illuminate\Database\Seeder;

class InstancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('instances')->delete();

        $permissions = array(
            array(
                'name' => 'Rutaip WebRTC',
                'websocket_proxy_url' => 'wss://phone.rutaip.net:8089/asterisk/ws',
                'outbound_proxy' => '',
                'ice_servers' => '[{ url:"stun.ekiga.net"}]',
                'rtcweb_breaker' => 'true',
                'video_enable' => 'false',
                ),
        );
        
        DB::table('instances')->insert($permissions);
    }
}
