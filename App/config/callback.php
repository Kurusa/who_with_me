<?php
return [
	'nextDist'    => \App\Commands\District::class,
	'prevDist'    => \App\Commands\District::class,
	'distDone'    => \App\Commands\District::class,
	'info'        => \App\Commands\QuestionInfo::class,
	'nextDistQ'   => \App\Commands\AddQuestion\SetDistrict::class,
	'prevDistQ'   => \App\Commands\AddQuestion\SetDistrict::class,
	'distDoneQ'   => \App\Commands\AddQuestion\SetDistrict::class,
	'yes'         => \App\Commands\AnswerQuestion::class,
	'no'          => \App\Commands\AnswerQuestion::class,
	'change'      => \App\Commands\AddQuestion\SetQuestionActivity::class,
	'changeDone'  => \App\Commands\AddQuestion\SetQuestionActivity::class,
	'update'      => \App\Commands\GetUpdates::class,
	'deleteQuestion' => \App\Commands\DeleteQuestion::class,
	'refresh'        => \App\Commands\RefreshQuestion::class,
	'moderate' => \App\Commands\AddQuestion\ModerateQuestion::class,
];