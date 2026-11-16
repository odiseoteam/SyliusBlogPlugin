// Same rules as the link mark of the server.
const HREF = /^(?:https?:\/\/|mailto:|tel:|\/(?!\/)|#)\S*$/i;

export function isAllowedHref(href) {
    return typeof href === 'string' && HREF.test(href);
}

/** The `rel` of a link, from its options: `nofollow`, `sponsored` or both. */
export function relOf({ nofollow = false, sponsored = false } = {}) {
    const tokens = [nofollow && 'nofollow', sponsored && 'sponsored'].filter(Boolean);

    return tokens.length > 0 ? tokens.join(' ') : null;
}

export function relTokens(rel) {
    return typeof rel === 'string' ? rel.split(/\s+/).filter(Boolean) : [];
}
