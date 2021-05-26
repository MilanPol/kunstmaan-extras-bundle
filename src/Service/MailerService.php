<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Service;

use Esites\KunstmaanExtrasBundle\ValueObject\Collections\AttachmentCollection;
use Esites\KunstmaanExtrasBundle\ValueObject\Collections\BaseUserCollection;
use InvalidArgumentException;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailerService
{
    private Environment $twigEnvironment;

    private Swift_Mailer $mailer;

    private ?string $fromEmail;

    private ?string $fromName;


    public function __construct(
        Environment $twigEnvironment,
        Swift_Mailer $mailer,
        ?string $fromEmail,
        ?string $fromName
    ) {
        $this->twigEnvironment = $twigEnvironment;
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendMailToEmailAddress(
        string $toEmail,
        string $template,
        array $data,
        string $subject,
        ?AttachmentCollection $attachments = null,
        ?array $bcc = null
    ): void {
        $html = $this->twigEnvironment->render(
            $template,
            $data
        );

        $this->validateParameters();

        $message = $this->buildMessage($toEmail, $subject, $html);

        $this->addAttachments($attachments, $message);

        if (is_array($bcc)) {
            foreach ($bcc as $bccEmail) {
                if (!is_string($bccEmail)) {
                    continue;
                }
                $message->addBcc($bccEmail);
            }
        }

        $this->mailer->send($message);
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendMail(
        BaseUser $user,
        string $template,
        array $data,
        string $subject,
        ?AttachmentCollection $attachments = null,
        ?BaseUserCollection $bcc = null
    ): void {
        $html = $this->twigEnvironment->render(
            $template,
            $data
        );

        $this->validateParameters();

        $message = $this->buildMessage($user->getEmail(), $subject, $html);

        $this->addAttachments($attachments, $message);

        if ($bcc instanceof BaseUserCollection) {
            /** @var BaseUser $bccUser */
            foreach ($bcc as $bccUser) {
                $message->addBcc($bccUser->getEmail());
            }
        }

        $this->mailer->send($message);
    }

    private function validateParameters(): void
    {
        if (!is_string($this->fromEmail)) {
            throw new InvalidArgumentException(
                'Define mailer_user in the bundles configuration to use the mailer service'
            );
        }

        if (!is_string($this->fromName)) {
            throw new InvalidArgumentException(
                'Define mailer_name in the bundles configuration to use the mailer service'
            );
        }
    }

    private function buildMessage(string $toEmail, string $subject, string $html): Swift_Message
    {
        return (new Swift_Message())
            ->setSubject($subject)
            ->setFrom(
                $this->fromEmail,
                $this->fromName
            )
            ->setTo($toEmail)
            ->setBody(
                $html,
                'text/html'
            )
            ;
    }

    private function addAttachments(?AttachmentCollection $attachments, Swift_Message $message): void
    {
        if ($attachments instanceof AttachmentCollection) {
            foreach ($attachments as $attachment) {
                $message->attach($attachment);
            }
        }
    }
}
