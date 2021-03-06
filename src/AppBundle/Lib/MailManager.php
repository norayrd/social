<?php
 
namespace AppBundle\Lib;
 
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
 
class MailManager
{
    protected $mailer;
    protected $twig;
    private $container;
 
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, Container $container)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->container = $container;
    }
 
    /**
     * Send email
     *
     * @param   string   $template      email template
     * @param   mixed    $parameters    custom params for template
     * @param   string   $to            to email address or array of email addresses
     * @param   string   $from          from email address
     * @param   string   $fromName      from name
     *
     * @return  boolean                 send status
     */
    public function sendEmail($template, $parameters, $to, $from, $fromName = null, $email_replay_to = null)
    {
        //$template = $this->twig->loadTemplate('AppBundle:Mail:' . $template . '.html.twig');
        $template = $this->twig->loadTemplate( $template );
 
        $subject  = $template->renderBlock('subject', $parameters);
        $bodyHtml = $template->renderBlock('body_html', $parameters);
        $bodyText = $template->renderBlock('body_text', $parameters);

        try {
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($from, $fromName)
                ->setTo($to)
                ->setBody($bodyHtml, 'text/html')
                ->addPart($bodyText, 'text/plain')
            ;

            if ($email_replay_to != null) {
                $message ->addReplyTo($email_replay_to);
            }

            $response = $this->mailer->send($message);
 
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
 
        return $response;
    }
}