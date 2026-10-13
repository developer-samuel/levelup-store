BLOCKED_PATTERNS = [
    # prompt injection
    "ignore previous instructions",
    "ignore all instructions",
    "ignore your instructions",
    "ignore all previous",
    "disregard previous",
    "disregard all",
    "forget previous",
    "forget your instructions",
    "forget everything",
    "override instructions",
    "bypass",
    "prompt injection",
    "your new instructions",
    "you are now",
    "you must now",
    "you should now",

    # persona hijacking
    "pretend you are",
    "pretend to be",
    "act as",
    "roleplay as",
    "new persona",
    "new role",

    # system access
    "developer mode",
    "jailbreak",
    "system:",
    "system prompt",
    "system message",
    "sudo",
    "run command",
    "do anything now",
    "without restrictions",
    "unrestricted mode",
    "base model",

    # data extraction
    "show me the database",
    "show me your prompt",
    "show your prompt",
    "what is your prompt",
    "reveal your instructions",
    "show your instructions",
    "what are your instructions",
    "sql query",
    "api key",
    "environment variable",
    "training data",

    # social engineering
    "as a developer",
    "as an admin",
    "as a tester",
    "in test mode",
    "maintenance mode",
    "debug mode",
    "admin mode",
    "ignore safety",
    "disable filters",

    # data exfiltration
    "send this to",
    "email this to",
    "forward this to",
    "copy this to",
    "export all",
    "dump all",

    # reconnaissance
    "what technology",
    "what framework",
    "what database",
    "what server",
    "show headers",
    "show config",

    # encoding bypass
    "base64",
    "hex decode",
    "url decode",
    "unicode escape",

    # prompt leaking
    "repeat after me",
    "say the following",
    "output the following",
    "print your instructions",
    "write out your prompt",

    # role confusion
    "you are a human",
    "you are not an ai",
    "you are not a bot",
    "forget you are an assistant",

    # indirect injection (via external content)
    "the following is a message from",
    "instructions from the user:",
    "new task:",
    "ignore the above",
    "disregard the above",
    "end of system prompt",
    "### instruction",
    "### system",
    "<|system|>",
    "<|user|>",
    "<|assistant|>",
    
    # sql injection
    "drop table",
    "select *",
    "insert into",
    "delete from",
    "update set",
    "union select",
    "or 1=1",
    "'; --",
    "exec(",
    "xp_cmdshell",
]
