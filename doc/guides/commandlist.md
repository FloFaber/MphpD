# Command lists

MPD supports what's called command lists. You can start a command list,
insert a few commands and then end, say execute, it.
No command is executed before the command list is ended.

It should mainly be used for commands which don't return anything other
than OK or ACK. `add` is such a command, for example.
However, you _can_ use it as you like with all commands the MPD protocol has to offer and
still get everything returned and parsed as you wish.

Keep in mind that command lists are only useful when performing a lot of commands at once as they are
designed to reduce overhead in such situations.
They do not help you much when adding 10 songs to the queue.
Use the library's built-in functions in combination with a loop for such small amounts.

The execution of a command list is stopped in case an error occurs.

## Methods

* [MphpD::bulk_start](../classes/MphpD#bulk_start)
* [MphpD::bulk_add](../classes/MphpD#bulk_add)
* [MphpD::bulk_end](../classes/MphpD#bulk_end)
* [MphpD::bulk_abort](../classes/MphpD#bulk_abort)

<br/>

You may want to have a look at the [MPD Protocol documentation](https://mpd.readthedocs.io/en/latest/protocol.html) for all available commands.
