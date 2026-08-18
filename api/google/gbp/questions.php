<?php
/**
 * GET  /api/google/gbp/questions.php        → { questions[], total, unanswered }
 * POST /api/google/gbp/questions.php
 *      { question_id, answer }              answer one question
 *      { faqs: [{question, answer}, …] }    publish generated FAQs as owner Q&A
 *
 * Needs the "My Business Q&A API" enabled on the Cloud project.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = getInput();

        if (!empty($input['faqs']) && is_array($input['faqs'])) {
            $res = $service->publishFaq($userId, $input['faqs']);
            sendSuccess(
                $res['posted'] > 0
                    ? "Posted {$res['posted']} question" . ($res['posted'] === 1 ? '' : 's') . ' to your listing'
                    : 'Google did not accept any of these',
                $res
            );
        }

        $res = $service->answerQuestion(
            $userId,
            (string)($input['question_id'] ?? ''),
            (string)($input['answer'] ?? '')
        );
        sendSuccess('Your answer is now public on Google', $res);
    }

    // Google's hard maximum for this endpoint is 10 per page.
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    sendSuccess('Questions', $service->listQuestions($userId, $limit));
});
