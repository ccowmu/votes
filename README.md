# votes

Voting app for club elections

# setup

Create a directory named `positions/`. In this directory, there should be a text
file per position, with a candidate per line.

For example if there are president and a vp positions, and foo and bar are
running for president, and baz and qux are running for vp, the setup looks
like:

```
$ mkdir positions
$ echo foo >>positions/president
$ echo bar >>positions/president
$ echo baz >>positions/vp
$ echo qux >>positions/vp
```

# output

The output is under the `votes/` directory. There is one subdirectory per
user, named by username. Under each user's directory there are files with
identical names to those in `positions/`, but with only one line: the users
vote for that position.
