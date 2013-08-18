<?php

namespace FPChat\Plugin;

abstract class AbstractPlugin
{
	/**
	 * Triggered on a chat line
	 *
	 * @param \FPChat\Bot $bot
	 * @param \FPChat\Line $line
	 */
	public function onLine($bot, $line) {}

	/**
	 * Triggered on a mention
	 *
	 * @param \FPChat\Bot $bot
	 * @param \FPChat\Line $line
	 */
	public function onMention($bot, $line) {}

	/**
	 * Triggered on a command
	 *
	 * @param \FPChat\Bot $bot
	 * @param \FPChat\Line $line
	 * @param string $command
	 * @param array $arguments
	 */
	public function onCommand($bot, $line, $command, $arguments) {}

	/**
	 * Triggered on quit
	 *
	 * @param \FPChat\Bot $bot
	 * @param \FPChat\User $user
	 */
	public function onQuit($bot, $user) {}

	/**
	 * Triggered on join
	 *
	 * @param \FPChat\Bot $bot
	 * @param \FPChat\User $user
	 */
	public function onJoin($bot, $user) {}

	/**
	 * Triggered before setup
	 *
	 * @param \FPChat\Bot $bot
	 */
	public function onPreSetup($bot) {}

	/**
	 * Triggered after setup
	 *
	 * @param \FPChat\Bot $bot
	 */
	public function onPostSetup($bot) {}

	/**
	 * Triggered each tick
	 *
	 * @param \FPChat\Bot $bot
	 */
	public function onTick($bot) {}

	/**
	 * Triggered each tick after everything else
	 *
	 * @param \FPChat\Bot $bot
	 */
	public function onPostTick($bot) {}

	/**
	 * Forces any pending data to be saved
	 *
	 * @param \FPChat\Bot $bot
	 */
	public function onSave($bot) {}

	/**
	 * Called when the bot is stopping
	 *
	 * @param \FPChat\Bot $bot
	 */
	public function onStop($bot) {}
}
