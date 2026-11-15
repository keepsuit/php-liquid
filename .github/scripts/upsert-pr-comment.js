// Creates or updates the bot comment identified by `marker`, so each push edits one comment instead of adding more.
module.exports = async ({ github, context, marker, body }) => {
  const issue_number = context.payload.pull_request.number;
  const { owner, repo } = context.repo;
  const comments = await github.paginate(github.rest.issues.listComments, { owner, repo, issue_number, per_page: 100 });
  const existing = comments.find((comment) => comment.user?.type === 'Bot' && comment.body?.includes(marker));
  const fullBody = `${marker}\n${body}`;

  if (existing) {
    await github.rest.issues.updateComment({ owner, repo, comment_id: existing.id, body: fullBody });
  } else {
    await github.rest.issues.createComment({ owner, repo, issue_number, body: fullBody });
  }
};
