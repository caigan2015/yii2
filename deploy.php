<?php
class Deploy
{
    public function deploy()
    {
        $commands = ['cd /var/www/html/yii2','git pull'];

        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE'];
        $payload = file_get_contents('php://input');
        //error_log($payload);
        //var_dump( 'sha1='.hash_hmac('sha1',$payload,'2e4dd3e73a4b2f854357ba21a8bdd3fc',false));die;
        if($this->isFromGithub($payload,$signature)){
            foreach ($commands as $command) {
                shell_exec($command);
            }
            http_response_code(200);
        }else{
            exit('error,bad request');
        }
    }

    private function isFromGithub($payload,$signature)
    {
        return 'sha1='.hash_hmac('sha1',$payload,'caigan',false) === $signature;
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $deploy = new Deploy();
    $deploy->deploy();
}
?>
