import logging
import logging.handlers

def init(main='main'):
    deflogger = logging.getLogger()
    for hdlr in deflogger.handlers:
        deflogger.removeHandler(hdlr)

    mainlogger = logging.getLogger(main)
    return mainlogger


def setup_console(mainlogger):
    handler = logging.StreamHandler()
    format = logging.Formatter('%(asctime)s %(levelname)-5s %(message)s', '%H:%M:%S')
    handler.setFormatter(format)
    handler.setLevel(logging.DEBUG)
    mainlogger.addHandler(handler)


def setup(log_path, stdout):
    mainlogger = init()

    # main to stderr
    if stdout:
        setup_console(mainlogger)

    # main to file
    handler = logging.handlers.TimedRotatingFileHandler(log_path+'/main.log', when='midnight', backupCount=10)
    format = logging.Formatter('%(asctime)s %(levelname)-5s %(message)s', '%y-%m-%d %H:%M:%S')
    handler.setFormatter(format)
    handler.setLevel(logging.DEBUG)
    mainlogger.addHandler(handler)

    # errors from main to extra file
    handler = logging.handlers.TimedRotatingFileHandler(log_path+'/error.log', when='midnight', backupCount=10)
    format = logging.Formatter('%(asctime)s %(levelname)-5s %(message)s', '%y-%m-%d %H:%M:%S')
    handler.setFormatter(format)
    handler.setLevel(logging.WARNING)
    mainlogger.addHandler(handler)

    # HTTP activity log
    handler = logging.handlers.TimedRotatingFileHandler(log_path+'/activity.log', when='midnight', backupCount=10)
    format = logging.Formatter('%(asctime)s %(message)s', '%y-%m-%d %H:%M:%S')
    handler.setFormatter(format)
    logger = logging.getLogger('activity')
    logger.addHandler(handler)
    logger.setLevel(logging.DEBUG)

    # SQL log
    handler = logging.handlers.TimedRotatingFileHandler(log_path+'/sql.log', when='midnight', backupCount=10)
    format = logging.Formatter('%(asctime)s %(message)s', '%y-%m-%d %H:%M:%S')
    handler.setFormatter(format)
    logger = logging.getLogger('sql')
    logger.addHandler(handler)
    logger.setLevel(logging.DEBUG)


def setup_cleaner(log_path, stdout):
    mainlogger = init('cleaner')

    # main to stderr
    if stdout:
        setup_console(mainlogger)

    # main to file
    handler = logging.handlers.TimedRotatingFileHandler(log_path+'/cleaner.log', when='midnight', backupCount=10)
    format = logging.Formatter('%(asctime)s %(levelname)-5s %(message)s', '%y-%m-%d %H:%M:%S')
    handler.setFormatter(format)
    handler.setLevel(logging.DEBUG)
    mainlogger.addHandler(handler)

    # SQL log
    handler = logging.handlers.TimedRotatingFileHandler(log_path+'/cleaner-sql.log', when='midnight', backupCount=10)
    format = logging.Formatter('%(asctime)s %(message)s', '%y-%m-%d %H:%M:%S')
    handler.setFormatter(format)
    logger = logging.getLogger('sql')
    logger.addHandler(handler)
    logger.setLevel(logging.DEBUG)


def finish():
    logging.shutdown()

