<?php 
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Workflow\Event\Event;

/**
 * 
 */
class WorkflowSubscriber implements EventSubscriberInterface
{
	private $mailer;
	
	function __construct(MailerInterface $mailer)
	{
		$this->mailer = $mailer;
	}

	public function newToyRequest(Event $event)
	{
		$email = (new Email())
			->from($event->getSubject()->getUser()->getEmail())
			->to('dad@test.com')
			->addTo('mum@test.com')
			->subject('Demande de jouet -'.$event->getSubject()->getName())
			->text('Bonjour Maman et Papa, merci de me commander le jouet : '. $event->getSubject()->getName() );

			$this->mailer->send($email);
	}

	public function toyReceived(Event $event)
	{
		$email = (new Email())
			->from('papa@test.com')
			->to($event->getSubject()->getUser()->getEmail())
			->subject('ton jouet est arriver')
			->text('ton Jouet est arrive, amuse toi bien!');

			$this->mailer->send($email);
	}

	public static function getSubscribedEvents()
	{
		return [
			'workflow.toy_request.leave.request' => 'newToyRequest',
			'workflow.toy_request.entered.received' => 'toyReceived',
		];
	}
}

