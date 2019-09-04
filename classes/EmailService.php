<?php
//require 'vendor/autoload.php';
require 'vendor/autoload.php';
        use Aws\Ses\SesClient;

class EmailService {
    public static function sendRegistrationEmailToSupervisors($requestorFirstName, $requestorLastName, $email)
    {

        //you need to put curl.cainfo=c:\xampp\php\cacert.pem
        //in php.ini... you can download that cacert from here:
        //http://curl.haxx.se/docs/caextract.html -- download the pem to that directory
        
        try {   
            $ses = SesClient::factory(array(
                    'credentials' => array(
                    'key' => 'are you the keymaster',
                    'secret' => 'c-tech astronomy'),
                    'region'      => 'us-east-1',
                    'version' => '2010-12-01',
                ));
            
            
        $supervisorEmails = array_map(function($e) { return $e->Email; }, UserRepository::GetSupervisors());
        
        //http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#sendemail
        $ses->sendEmail(
                [
                    'Destination' => [
                        'ToAddresses' => $supervisorEmails
                    ],
                    'Message' => [
                        'Body' => [
                            'Text' => [
                                'Data' => $requestorFirstName . ' ' . $requestorLastName . 'is requesting access to the Mavericks Sales Portal with the email address ' . $email . '.  Please login to approve or reject the request.',
                            ],
                        ],
                        'Subject' => [
                            'Data' => 'Access Request for ' . $requestorFirstName . ' ' . $requestorLastName,
                        ],
                    ],
                    'Source' => 'someone@somewhere',
                ]);
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
       
    }
    
    public static function sendRegistrationApprovedEmail($requestorFirstName, $requestorLastName, $email, $LinkId)
    {
        //this one should have the activation code and go to requestor
        $body = 'Dear ' . $requestorFirstName . ' ' . $requestorLastName . ', ' . "\r\n" . 
                'Your account with Maverick Sales Portal has been approved.  The last step is to activate your account by'
                . 'clicking the link below:' . "\r\n" .
                'http://mavericksalesportal.ddns.net/#/activate/' . $LinkId;
        self::sendEmail([$email],'Your Maverick Sales Portal account Has been approved', $body);
        
        
        
    }
    
    
    public static function sendEmail($ToAddresses, $Subject, $Body)
    {
         try {   
            $ses = self::getEmailSession();
                            
        //this will get sandboxed until we ask for "production access"
            $ses->sendEmail(
                [
                    'Destination' => [
                        'ToAddresses' => $ToAddresses
                    ],
                    'Message' => [
                        'Body' => [
                            'Text' => [
                                'Data' => $Body,
                            ],
                        ],
                        'Subject' => [
                            'Data' => $Subject,
                        ],
                    ],
                    'Source' => 'someone@somewhere',
                ]);
        }
        
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
        
    }
    
    private static function getEmailSession()
    {
        return SesClient::factory(array(
                    'credentials' => array(
                    'key' => 'are you the keymaster',
                    'secret' => 'c-tech astronomy'),
                    'region'      => 'us-east-1',
                    'version' => '2010-12-01',
                ));
        
    }
    
    public static function sendRegistrationRejectedEmail($requestorFirstName, $requestorLastName, $email)
    {
        //this one should just tell the user they were not approved
    }
    
    //todo: request productino access so we can test this method.
     public static function sendRegistrationReceivedEmail($requestorFirstName, $requestorLastName, $email)
    {
        try {   
            $ses = SesClient::factory(array(
                    'credentials' => array(
                    'key' => 'are you the keymaster',
                    'secret' => 'c-tech astronomy'),
                    'region'      => 'us-east-1',
                    'version' => '2010-12-01',
                ));
                            
        //this will get sandboxed until we ask for "production access"
        $ses->sendEmail(
                [
                    'Destination' => [
                        'ToAddresses' => [$email]
                    ],
                    'Message' => [
                        'Body' => [
                            'Text' => [
                                'Data' => 'Dear ' . $requestorFirstName . ' ' . $requestorLastName . ', ' . "\r\n" . 'Thank you for your recent registration request.  An e-mail has been sent to the supervisor group for review and you will receive another e-mail once a supervisor approves or rejects your request.',
                            ],
                        ],
                        'Subject' => [
                            'Data' => 'Maverick Sales Portal - Your registration has been received.',
                        ],
                    ],
                    'Source' => 'someone@somewhere',
                ]);
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
       
    }
}
?>
