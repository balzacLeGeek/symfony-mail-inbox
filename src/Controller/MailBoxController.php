<?php

namespace App\Controller;

use SecIT\ImapBundle\Service\Imap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mailbox")
 */
class MailBoxController extends AbstractController
{
    CONST MAX_EMAIL = 10;

    /**
     * @Route("/", name="mailbox_inbox")
     */
    public function inbox(Imap $imap)
    {
        $gmailConnection = $imap->get('gmail_connection');

        // count: flags, messages, recent, unseen, uidnext, and uidvalidity
        $statusMailbox = $gmailConnection->statusMailbox();

        // 5 days from current date
        $date = (new \DateTime('NOW'))->modify('-5 day');

        $recentEmails = $gmailConnection->searchMailbox('SINCE "' . $date->format('j F Y') . '"');

        $emails = [];

        for ($i=0; $i < self::MAX_EMAIL; $i++) {
            $emails[] = $gmailConnection->getMailHeader($recentEmails[$i]);
        }

        return $this->render('mailbox/index.html.twig', [
            'statusMailbox' => $statusMailbox,
            'emails' => $emails,
        ]);
    }
}
