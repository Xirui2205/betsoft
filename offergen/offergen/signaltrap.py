import signal
from error import TerminatedError

def sig_handler(signl, frme):
    raise TerminatedError()
    return 0


def init():
    signal.signal(signal.SIGINT, sig_handler)
    signal.signal(signal.SIGTERM, sig_handler)
    signal.signal(signal.SIGHUP, sig_handler)

