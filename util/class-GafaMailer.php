<?php
/**
 * Class GafaMailer
 */
class GafaMailer {

    /**
     * Directory where the mails are saved relative to the template directory. Default: "mails".
     * @var string
     */
    private $mailsDirectory = "mails";

    /**
     * Headers used to send the email by default. Default: array('Content-Type: text/html; charset=UTF-8').
     * @var string|array
     */
    private $defaultHeaders = array('Content-Type: text/html; charset=UTF-8');

    /**
     * Headers used to send the email by default. Default "".
     * @var string|array
     */
    private $defaultTo = "";

    /**
     * Filter to set the template-relative path where the mails are saved.
     *      Filter arg 0: The default directory name.
     */
    const Filter_GafaMailerMailsDirectory = "gafa_mailer_mails_directory";

    /**
     * Filter to set the default headers the mails are sent with.
     *      Filter arg 0: The default headers.
     */
    const Filter_GafaMailerDefaultHeaders = "gafa_mailer_default_headers";
    /**
     * Filter to set the email of the default receiver the mails are sent to.
     *      Filter arg 0: The default email of the receiver.
     */
    const Filter_GafaMailerDefaultTo = "gafa_mailer_default_to";

    /**
     * GafaMailer constructor.
     */
    private function __construct()
    {
        $this->mailsDirectory   = apply_filters(GafaMailer::Filter_GafaMailerMailsDirectory, $this->mailsDirectory);
        $this->defaultHeaders   = apply_filters(GafaMailer::Filter_GafaMailerDefaultHeaders, $this->defaultHeaders);
        $this->defaultTo        = apply_filters(GafaMailer::Filter_GafaMailerDefaultTo, $this->defaultTo);

        if(!file_exists($this->MailsPath())) {
            throw new Exception("The directory {$this->MailsPath()} does not exist.");
        }
    }

    /**
     * @var GafaMailer
     */
    private static $instance = null;

    /**
     * Gets the instance (the only one) of the GafaMailer.
     * @return GafaMailer
     */
    public static function Instance(){
        if(GafaMailer::$instance === null) {
            GafaMailer::$instance = new GafaMailer();
        }
        return GafaMailer::$instance;
    }

    /**
     * Returns the absolute path where the mails are saved.
     * @return string
     */
    public function MailsPath(){
        return get_template_directory() . DIRECTORY_SEPARATOR . $this->mailsDirectory;
    }

    /**
     * Returns the path where an email is saved.
     * @param string $emailFileName file name of the email.
     * @return string the path of the email.
     */
    public function EmailPath($emailFileName){
        return $this->MailsPath() . DIRECTORY_SEPARATOR . $emailFileName;
    }

    /**
     * Sends an email.
     * @param string|null $toOrDefault. Email address where to send the email. Use null to use the default receiver's email.
     * @param string $subject. Subject of the email.
     * @param string $emailFileName. Name of the file that 'echoes' the email, relative to the path defined by GafaMailer::MailsPath.
     * @param array $replacements. The content echoed by the file $emailFileName, will be string-translated, using an array of constant-replacement strings (see the 'strtr' php function to see how this works).
     * @param string|array|null $headersOrDefault. Headers the email will be sent with. Use null to use the default headers.
     * @param string|array $attachments. Attachments for the email.
     * @return bool returns true if the email was sent, false otherwise.
     * @deprecated Use the function 'Send'.
     */
    public function SendEmail($toOrDefault, $subject, $emailFileName, $replacements = array(), $headersOrDefault = null, $attachments = array()){

        // Set the default 'to' if required.

        if($toOrDefault === null) {
            $toOrDefault = $this->defaultTo;
        }

        // Set the default 'headers' if required.

        if($headersOrDefault === null) {
            $headersOrDefault = $this->defaultHeaders;
        }

        return wp_mail($toOrDefault, $subject, $this->GetEmailHtml($emailFileName, $replacements), $headersOrDefault, $attachments);
    }

    /**
     * Sends an email.
     *
     * @param string|null $toOrDefault . Email address where to send the email. Use null to use the default receiver's email that was defined by the filter 'GafaMailer::Filter_GafaMailerDefaultTo'.
     * @param string $subject . Subject of the email.
     * @param string $emailFileName . Name of the file that 'echoes' the email, relative to the path defined by GafaMailer::MailsPath.
     * @param array $vars . Contains an array of the varaibles that will be extracted so the $emailFileName file gets access to them.
     * @param string|array|null $headersOrDefault . Headers the email will be sent with. Use null to use the default headers.
     * @param string|array $attachments . Attachments for the email.
     * @param null|string $mailHtml (Optional, reference). Will be overridden with the email's html.
     * @return bool returns true if the email was sent, false otherwise.
     */
    public function Send($toOrDefault, $subject, $emailFileName, $vars = array(), $headersOrDefault = null, $attachments = array(), &$mailHtml = null){

        // Set the default 'to' if required.

        if($toOrDefault === null) {
            $toOrDefault = $this->defaultTo;
        }

        // Set the default 'headers' if required.

        if($headersOrDefault === null) {
            $headersOrDefault = $this->defaultHeaders;
        }

        $mailHtml = $this->EmailHtml($emailFileName, $vars);

        return wp_mail($toOrDefault, $subject, $mailHtml, $headersOrDefault, $attachments);
    }

    /**
     * Gets the email message content.
     *
     * We intentionally used the prefix "_gafa_" in the name of the parameters to reduce the risk of name collision.
     * So the variables "$_gafa_emailFileName" and "$_gafa_vars" will be present in the scope of the email.
     *
     * In the odd case you have a variable called the same as one of the parameters of this function, that variable name will be prefixed
     * with "gafa_", so, if you have a variable called $_gafa_vars, it will become '$gafa__gafa_vars' in the scope of the email.
     *
     * @param $_gafa_emailFileName
     * @param array $_gafa_vars . Contains an array of the varaibles that will be extracted so the $emailFileName file gets access to them.
     * @return string the html of the email.
     */
    public function EmailHtml($_gafa_emailFileName, $_gafa_vars = array()){
        ob_start();
        if(file_exists($this->EmailPath($_gafa_emailFileName))) {
            extract($_gafa_vars, EXTR_PREFIX_SAME, "gafa_");
            require($this->EmailPath($_gafa_emailFileName));
        } else {
            gafa("The file ".$this->EmailPath($_gafa_emailFileName)." does not exist.", "Error");
        }
        return ob_get_clean();
    }

    /**
     * Gets the email message content.
     * @param $emailFileName
     * @param $replacements
     * @deprecated use 'EmailHtml'.
     * @return string
     */
    public function GetEmailHtml($emailFileName, $replacements){
        ob_start();
        $emailFilePath = $this->MailsPath() . DIRECTORY_SEPARATOR . $emailFileName;
        if(file_exists($emailFilePath)) {
            require($emailFilePath);
        } else {
            gafa("The file $emailFilePath does not exist.", "Error");
        }
        $message = ob_get_clean();
        return strtr($message, $replacements);
    }
}