INSERT INTO `users` (`id`, `email`, `first_name`, `last_name`, `date_joined`) VALUES
(1, 'em1@email.com', 'john', 'smith', '2017-04-21'),
(2, 'em2@email.com', 'sam', 'johnson', '2017-04-20'),
(3, 'em3@email.com', 'james', 'jones', '2017-04-24'),
(4, 'em4@email.com', 'sara', 'smith', '2017-04-23'),
(5, 'dconns11@gmail.com', 'dan', 'connor', '2017-04-21'),
(6, 'em6@email.com', 'emily', 'smith', '2017-04-21');

-- --------------------------------------------------------

INSERT INTO `muscle_groups` (`id`, `name`) VALUES
(1, 'Cardio'),
(2, 'Chest'),
(3, 'Shoulders'),
(4, 'Abs'),
(5, 'Calf Raise');

-- --------------------------------------------------------


INSERT INTO `exercises` (`id`, `exercise_name`, `muscle_group_id`) VALUES
(1, 'Treadmill', 1),
(2, 'Bench Press', 2),
(3, 'Shoulder Press', 3),
(4, 'Plank', 4),
(5, 'TreadClimber', 1),
(6, 'Walking', 1),
(7, 'Calf Raise', 5);

-- --------------------------------------------------------


INSERT INTO `workouts` (`id`, `name`, `description`) VALUES
(1, 'test plan 1', 'Cardio plan'),
(2, 'test plan 2', 'Chest and shoulders');

-- --------------------------------------------------------

INSERT INTO `days` (`id`, `day_name`, `plan_id`) VALUES
(1, 'run day', 1),
(2, 'walk day', 1),
(3, 'run day', 1),
(4, 'chest day', 2),
(5, 'shoulders day', 2);

-- --------------------------------------------------------

INSERT INTO `workout_exercises` (`id`, `exercise_id`, `plan_id`, `day_id`, `duration`, `repetitions`, `weight`) VALUES
(1, 1, 1, 1, 3000, 1, 0.0),
(2, 6, 1, 2, 3000, 1, 0.0),
(3, 1, 1, 1, 3200, 1, 0.0),
(4, 6, 1, 2, 3200, 1, 0.0),
(5, 2, 2, 4, 0, 10, 35.0),
(6, 3, 2, 5, 0, 10, 25.0),
(7, 2, 2, 5, 0, 12, 40.0),
(8, 3, 2, 5, 0, 12, 27.0);

-- --------------------------------------------------------

INSERT INTO `user_workouts` (`user_id`, `plan_id`) VALUES
(1, 2),
(2, 1);

-- --------------------------------------------------------




