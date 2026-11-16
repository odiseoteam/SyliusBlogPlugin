/** Updates the selected block of this type, or adds one: after the selected block, or at the cursor. */
export function insertOrUpdate(type, attrs, state, commands) {
    const { selection } = state;
    if (selection.node?.type.name === type) {
        return commands.updateAttributes(type, attrs);
    }

    const content = { type, attrs };

    return selection.node ? commands.insertContentAt(selection.to, content) : commands.insertContent(content);
}
