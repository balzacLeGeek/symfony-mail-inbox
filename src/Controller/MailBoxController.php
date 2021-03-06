<?php

namespace App\Controller;

use SecIT\ImapBundle\Service\Imap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/mailbox")
 */
class MailBoxController extends AbstractController
{
    CONST MAX_EMAIL = 10;

    public function sidebar(Imap $imap)
    {
        $gmailConnection = $imap->get('gmail_connection');

        // count: flags, messages, recent, unseen, uidnext, and uidvalidity
        $statusMailbox = $gmailConnection->statusMailbox();

        return $this->render('mailbox/components/sidebar.html.twig', [
            'statusMailbox' => $statusMailbox,
        ]);
    }
    
    /**
     * @Route("/", name="mailbox_inbox")
     */
    public function inbox(Imap $imap)
    {
        $gmailConnection = $imap->get('gmail_connection');

        // 5 days from current date
        $date = (new \DateTime('NOW'))->modify('-5 day');

        $recentEmails = $gmailConnection->searchMailbox('SINCE "' . $date->format('j F Y') . '"');

        $emails = [];

        for ($i=0; $i < self::MAX_EMAIL; $i++) {
            $emails[] = $gmailConnection->getMailHeader($recentEmails[$i]);
        }

        return $this->render('mailbox/index.html.twig', [
            'emails' => $emails,
        ]);
    }

    /**
     * @Route("/read/{mailId}", name="mailbox_read")
     */
    public function read(Imap $imap, Request $request, $mailId)
    {
        $gmailConnection = $imap->get('gmail_connection');

        $email = $gmailConnection->getMail($mailId, true);

        return $this->render('mailbox/read.html.twig', [
            'email' => $email,
        ]);
    }
}
