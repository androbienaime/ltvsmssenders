<?php

namespace ltvsmssenders;


use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;

class SendSMS
{
    protected $region;
    protected $version;
    protected $credentials = array();

    /**
     * SnsClient constructor.
     * @param array $credentials
     * @param string $region
     * @param string $version
     */
    public function __construct(array $credentials, $region="us-east-1", $version="2010-03-31")
    {
        $this->region = $region;
        $this->version = $version;
        $this->credentials = $credentials;
    }

    /**
     * @return SnsClient
     */
    public function SnsClient(){
        $SnSClient = NULL;

        try{
            $SnSClient = new SnsClient([
                'region' => $this->region,
                'version' => $this->version,
                'credentials' => $this->credentials
            ]);

        }catch(AwsException $e){
            error_log($e->getMessage());
        }


        return $SnSClient;
    }

    /**
     * @param $message
     * @param $phone
     * @param null $senderId
     * @return mixed
     */
    public function send($message, $phone, $senderId = Null){
        $args = array(
            'MessageAttributes' =>[
                'AWS.SNS.SMS.SenderID' => [
                    'DataType' => 'String',
                    'StringValue' => $senderId
                ]
            ],
            'SMSType' => "Transactional",
            'Message' => $message,
            'PhoneNumber' => $phone
        );


        if($this->SnsClient() != NULL) {
            return $this->SnsClient()->publish($args);
        }

    }


}
