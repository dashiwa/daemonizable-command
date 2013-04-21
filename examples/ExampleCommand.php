<?php

namespace Acme\DemoBundle\Command;

use Wrep\Daemonizable\Command\EndlessCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class ExampleCommand extends EndlessCommand
{
	// This is just a normal Command::configure() method
	protected function configure()
	{
		$this->setName('acme:examplecommand')
			 ->setDescription('A demo of the EndlessCommand class')
			 ->setTimeout(1.5); // Set the timeout in seconds between two calls to the "execute" method
	}

	// This is a normal Command::initialize() method and it's called exactly once before the first execute call
	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		// Do one time initialization here
	}

	// Execute will be called in a endless loop
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// Tell the user what we're going to do.
		// This will be a NullOutput if the user doesn't want any output at all,
		//  so you don't have to do any checks, just always write to the output.
		$output->write('Updating average score ... ');

		// After a long operation, but before doing irreversable things call throwExceptionOnShutdown
		//  this will throw an exception if the OS or something else wants us to shutdown. Finalize is
		//  still called and the command will exit normally.
		$score = $this->calculateAvgScore();
		$this->throwExceptionOnShutdown();

		if ( false === file_put_contents('/tmp/acme-avg-score.txt', $score) )
		{
			// Set the returncode tot non-zero if there are any errors
			$this->setCode(1);

			// After this execute method returns we want the command exit
			$this->shutdown();

			// Tell the user we're done
			$output->writeln('failed!');
		}
		else
		{
			// Tell the user we're done
			$output->writeln('done');
		}
	}

	// Called once on shutdown after the last iteration finished
	protected function finalize(InputInterface $input, OutputInterface $output)
	{
		// Do some cleanup here

		// Keep it short! We may need to exit because the OS wants to shutdown
		// and we can get killed if it takes to long!
	}

	// Long operation to calculate the avarage score
	private function calculateAvgScore()
	{
		sleep(5);
		return rand(1, 10);
	}
}