/*
PostgreSQL Backup
Database: db/public
Backup Time: 2025-01-18 18:55:18
*/

DROP SEQUENCE IF EXISTS "public"."activity_levels_id_seq";
DROP SEQUENCE IF EXISTS "public"."deleted_records_id_seq";
DROP SEQUENCE IF EXISTS "public"."roles_id_seq";
DROP SEQUENCE IF EXISTS "public"."user_details_id_seq";
DROP SEQUENCE IF EXISTS "public"."users_id_seq";
DROP TABLE IF EXISTS "public"."activity_levels";
DROP TABLE IF EXISTS "public"."deleted_records";
DROP TABLE IF EXISTS "public"."roles";
DROP TABLE IF EXISTS "public"."user_details";
DROP TABLE IF EXISTS "public"."user_roles";
DROP TABLE IF EXISTS "public"."users";
DROP FUNCTION IF EXISTS "public"."calculate_daily_calories(p_weight int4, p_height int4, p_age int4, p_gender varchar, p_activity_level int4)";
DROP FUNCTION IF EXISTS "public"."log_deleted_records()";
DROP FUNCTION IF EXISTS "public"."reset_consumed_calories()";
DROP FUNCTION IF EXISTS "public"."trigger_calculate_daily_calories_after_update()";
CREATE SEQUENCE "activity_levels_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;
CREATE SEQUENCE "deleted_records_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;
CREATE SEQUENCE "roles_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;
CREATE SEQUENCE "user_details_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;
CREATE SEQUENCE "users_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;
CREATE TABLE "activity_levels" (
  "id" int4 NOT NULL DEFAULT nextval('activity_levels_id_seq'::regclass),
  "level_name" varchar(50) COLLATE "pg_catalog"."default" NOT NULL
)
;
ALTER TABLE "activity_levels" OWNER TO "docker";
CREATE TABLE "deleted_records" (
  "id" int4 NOT NULL DEFAULT nextval('deleted_records_id_seq'::regclass),
  "table_name" text COLLATE "pg_catalog"."default" NOT NULL,
  "deleted_data" jsonb NOT NULL,
  "deleted_at" timestamp(6) NOT NULL DEFAULT now()
)
;
ALTER TABLE "deleted_records" OWNER TO "docker";
CREATE TABLE "roles" (
  "id" int4 NOT NULL DEFAULT nextval('roles_id_seq'::regclass),
  "role_name" varchar(50) COLLATE "pg_catalog"."default" NOT NULL
)
;
ALTER TABLE "roles" OWNER TO "docker";
CREATE TABLE "user_details" (
  "id" int4 NOT NULL DEFAULT nextval('user_details_id_seq'::regclass),
  "user_id" int4 NOT NULL,
  "gender" varchar(10) COLLATE "pg_catalog"."default" NOT NULL,
  "height" int4 NOT NULL,
  "weight" int4 NOT NULL,
  "age" int4 NOT NULL,
  "activity_level_id" int4,
  "daily_calories" int4 NOT NULL DEFAULT 0,
  "consumed_calories" int4 DEFAULT 0,
  "daily_protein" int4 NOT NULL DEFAULT 0,
  "daily_fat" int4 NOT NULL DEFAULT 0,
  "daily_carbohydrates" int4 NOT NULL DEFAULT 0,
  "consumed_protein" int4 DEFAULT 0,
  "consumed_fat" int4 DEFAULT 0,
  "consumed_carbohydrates" int4 DEFAULT 0
)
;
ALTER TABLE "user_details" OWNER TO "docker";
CREATE TABLE "user_roles" (
  "user_id" int4 NOT NULL,
  "role_id" int4 NOT NULL
)
;
ALTER TABLE "user_roles" OWNER TO "docker";
CREATE TABLE "users" (
  "id" int4 NOT NULL DEFAULT nextval('users_id_seq'::regclass),
  "first_name" varchar(50) COLLATE "pg_catalog"."default" NOT NULL,
  "last_name" varchar(50) COLLATE "pg_catalog"."default" NOT NULL,
  "email" varchar(100) COLLATE "pg_catalog"."default" NOT NULL,
  "password" varchar(255) COLLATE "pg_catalog"."default" NOT NULL
)
;
ALTER TABLE "users" OWNER TO "docker";
CREATE TABLE "public"."nutrition_tips" (
  "id" int4 NOT NULL DEFAULT nextval('nutrition_tips_id_seq'::regclass),
  "hour_start" int4 NOT NULL,
  "hour_end" int4 NOT NULL,
  "tip" text COLLATE "pg_catalog"."default" NOT NULL,
  "benefit" text COLLATE "pg_catalog"."default" NOT NULL,
  CONSTRAINT "nutrition_tips_pkey" PRIMARY KEY ("id")
)
;

ALTER TABLE "public"."nutrition_tips" 
  OWNER TO "docker";
CREATE OR REPLACE FUNCTION "calculate_daily_calories"("p_weight" int4, "p_height" int4, "p_age" int4, "p_gender" varchar, "p_activity_level" int4)
  RETURNS TABLE("daily_calories" int4, "daily_protein" int4, "daily_fat" int4, "daily_carbohydrates" int4) AS $BODY$
DECLARE
    bmr INT;
    activity_multiplier FLOAT;
    calories_from_protein INT;
    calories_from_fat INT;
    calories_from_carbohydrates INT;
BEGIN
    -- Komunikaty debugujące
    RAISE NOTICE 'Input values - Weight: %, Height: %, Age: %, Gender: %, Activity Level: %', p_weight, p_height, p_age, p_gender, p_activity_level;

    -- Obliczanie BMR w zależności od płci
    IF p_gender = 'male' THEN
        bmr := 88.362 + (13.397 * p_weight) + (4.799 * p_height) - (5.677 * p_age);
    ELSE
        bmr := 447.593 + (9.247 * p_weight) + (3.098 * p_height) - (4.330 * p_age);
    END IF;

    -- Mapowanie poziomu aktywności na wartość mnożnika
    CASE p_activity_level
        WHEN 1 THEN activity_multiplier := 1.2;   -- Siedzący tryb życia
        WHEN 2 THEN activity_multiplier := 1.375; -- Lekka aktywność
        WHEN 3 THEN activity_multiplier := 1.55;  -- Średnia aktywność
        WHEN 4 THEN activity_multiplier := 1.725; -- Wysoka aktywność
        WHEN 5 THEN activity_multiplier := 1.9;   -- Bardzo wysoka aktywność
        ELSE activity_multiplier := 1.2;          -- Domyślnie siedzący tryb życia
    END CASE;

    -- Obliczenie dziennego zapotrzebowania kalorycznego
    daily_calories := ROUND(bmr * activity_multiplier);

    -- Obliczenia makroskładników
    calories_from_protein := ROUND(2 * p_weight * 4); -- 2g białka na kg masy ciała
    daily_protein := ROUND(calories_from_protein / 4); -- w gramach

    calories_from_fat := ROUND(daily_calories * 0.25); -- 25% kalorii z tłuszczów
    daily_fat := ROUND(calories_from_fat / 9); -- w gramach

    calories_from_carbohydrates := daily_calories - (calories_from_protein + calories_from_fat);
    daily_carbohydrates := ROUND(calories_from_carbohydrates / 4); -- w gramach

    RETURN QUERY SELECT daily_calories, daily_protein, daily_fat, daily_carbohydrates;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;
ALTER FUNCTION "calculate_daily_calories"("p_weight" int4, "p_height" int4, "p_age" int4, "p_gender" varchar, "p_activity_level" int4) OWNER TO "docker";
CREATE OR REPLACE FUNCTION "log_deleted_records"()
  RETURNS "pg_catalog"."trigger" AS $BODY$
BEGIN
    INSERT INTO deleted_records (table_name, deleted_data, deleted_at)
    VALUES (TG_TABLE_NAME, row_to_json(OLD)::JSONB, NOW());
    RETURN OLD;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION "log_deleted_records"() OWNER TO "docker";
CREATE OR REPLACE FUNCTION "reset_consumed_calories"()
  RETURNS "pg_catalog"."void" AS $BODY$
BEGIN
    UPDATE user_details SET consumed_calories = 0;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION "reset_consumed_calories"() OWNER TO "docker";
CREATE OR REPLACE FUNCTION "trigger_calculate_daily_calories_after_update"()
  RETURNS "pg_catalog"."trigger" AS $BODY$
DECLARE
    result RECORD;
BEGIN
    -- Wywołanie funkcji calculate_daily_calories
    SELECT * INTO result FROM calculate_daily_calories(
        NEW.weight,
        NEW.height,
        NEW.age,
        NEW.gender,
        NEW.activity_level_id
    );

    -- Aktualizacja pól w tabeli
    NEW.daily_calories := result.daily_calories;
    NEW.daily_protein := result.daily_protein;
    NEW.daily_fat := result.daily_fat;
    NEW.daily_carbohydrates := result.daily_carbohydrates;

    RETURN NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION "trigger_calculate_daily_calories_after_update"() OWNER TO "docker";
BEGIN;
LOCK TABLE "public"."activity_levels" IN SHARE MODE;
DELETE FROM "public"."activity_levels";
INSERT INTO "public"."activity_levels" ("id","level_name") VALUES (1, 'sedentary'),(2, 'light'),(3, 'moderate'),(4, 'active'),(5, 'very_active')
;
COMMIT;
BEGIN;
LOCK TABLE "public"."deleted_records" IN SHARE MODE;
DELETE FROM "public"."deleted_records";
INSERT INTO "public"."deleted_records" ("id","table_name","deleted_data","deleted_at") VALUES (1, 'users', '{"id": 34, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$ICDjBeyKUmo7vxjnfkr2/.s7t.qKQ9jNklAvY2cfeH287xNe/gU2m", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-04 17:33:59.340679'),(2, 'users', '{"id": 36, "email": "andrzej@onet.pl", "password": "$2y$10$mLYAMX8Alh5aQL8yOTHUz.FUBq9ElozVzHIVe4ba.4Ctc3ADUJR6W", "last_name": "Mazur", "first_name": "Andrzej"}', '2025-01-05 15:40:44.555985'),(3, 'users', '{"id": 37, "email": "kacper@onet.pl", "password": "$2y$10$GG2hTUdRtVaFoQ4bF473De5.Ww7GIawJQ.gnnPa2XqDUCc6MReiW.", "last_name": "Mrowiec", "first_name": "Kacper"}', '2025-01-05 15:40:54.723233'),(4, 'users', '{"id": 38, "email": "kacper@onet.pl", "password": "$2y$10$5m9O2Z4273vhk6na9cN2.eGbQJgWunx23tHURdmVssSs91Te2XPOe", "last_name": "Mrowiec", "first_name": "Kacper"}', '2025-01-05 15:42:34.745254'),(5, 'users', '{"id": 35, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$iE7incGV.zTkHjDONlcppOXhyoaU3IRYgKeUL1NshMqQ0ZI1XJGgG", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-05 15:55:50.091158'),(6, 'users', '{"id": 39, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$XfbxQyMDNW10aBUCzz68meUEYMe9fa/7sKsA2AoITDNOBmxLKwT56", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-06 18:46:43.533058'),(7, 'users', '{"id": 40, "email": "patrix.p100@gmail.com", "password": "$2y$10$/Ehhr85BKZkwbEX/.XeuoOpLo9VvH3sfGMJZn7c/M1TWR9XV6QybC", "last_name": "Przybyciński", "first_name": "Patryk"}', '2025-01-06 18:46:43.533058'),(8, 'users', '{"id": 41, "email": "kacper@onet.pl", "password": "$2y$10$dbJfkbTPfJPZCz1tovcQ6uCOyso0ym3kwM46LLo8u9sxeqBTAIq9u", "last_name": "Mazur", "first_name": "Kacper"}', '2025-01-06 18:46:43.533058'),(9, 'users', '{"id": 42, "email": "admin@wdpai.pl", "password": "$2y$10$g.NxfRm6L9343.7bThzLuu1gdGl2hHWLGrClUMkXqeAHoYzja3cyG", "last_name": "qw", "first_name": "pat"}', '2025-01-06 18:46:43.533058'),(10, 'users', '{"id": 43, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$orpXKFsBd/Oss/a7I6xy/.pDHV5.j10RRc5zh86q1E5ksEsm43b92", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-07 11:08:50.916722'),(11, 'users', '{"id": 44, "email": "kacper@onet.pl", "password": "$2y$10$YZfILYVEDUxRLtB.C.L6ou4OPHi4og9pkICfuvBYZRdn8p26AeNJO", "last_name": "Mrowiec", "first_name": "Kacper"}', '2025-01-07 11:08:50.916722'),(12, 'users', '{"id": 45, "email": "andrzej@onet.pl", "password": "$2y$10$9PdJymlwIQI/KirmTyA7XuFUWykeZAQlwnY/zUeRsIkgg2yJH5pUm", "last_name": "Mazur", "first_name": "Andrzej"}', '2025-01-07 11:08:50.916722'),(13, 'users', '{"id": 46, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$fQY58vdblHX4Bnxho1PAJ.SHBD8.p03qOigwz/kWKbpBq5XAYs0Nm", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-07 17:39:01.176296'),(14, 'users', '{"id": 47, "email": "andrzej@onet.pl", "password": "$2y$10$n7xoDJ0hIyt19Y.JDuC3hu.mXbcVi/gdh/lpN0S0GTFpg.LlOrBfe", "last_name": "Mazur", "first_name": "Andrzej"}', '2025-01-07 17:39:01.176296'),(15, 'users', '{"id": 48, "email": "kacper@onet.pl", "password": "$2y$10$3PV/NQv2CyiM2rtcMO5A.ulJX4F/D4ZMxsa4PEskXbUHdaJzBsRY.", "last_name": "Mrowiec", "first_name": "Kacper"}', '2025-01-07 17:39:01.176296'),(16, 'users', '{"id": 49, "email": "patrix.p100@gmail.com", "password": "$2y$10$qobRkDJuPwgtumZBbjIFIeSaLeb0NbAiZV7i0fSdCvr8q26EaMCBC", "last_name": "Przybyciński", "first_name": "Patryk"}', '2025-01-07 17:39:01.176296'),(17, 'users', '{"id": 50, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$E8UjoBQCOcf9dVNXLB/tdOJFs1iwIjvaW1tiQXd9TTdXVwknV0AA6", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-07 17:50:09.961984'),(18, 'users', '{"id": 51, "email": "andrzej@onet.pl", "password": "$2y$10$Uudezjucubqd/Jl.8/yCg.btRcVrupE2W0IFtr95LdGM4zp2933m6", "last_name": "Mazur", "first_name": "Andrzej"}', '2025-01-07 17:50:09.961984'),(19, 'users', '{"id": 53, "email": "kacper@onet.pl", "password": "$2y$10$PN8NwtcN9vXqeuhyLRX7ZuyYbFcxsrc4qyaVgW4ryiWcCtgUaxsuO", "last_name": "Mrowiec", "first_name": "Kacper"}', '2025-01-07 18:17:26.490408'),(20, 'users', '{"id": 54, "email": "andrzej@onet.pl", "password": "$2y$10$HI5B3xZAqkTAhD4o9ndfuurEmenKvMUvJuxL83lBgYeXGXNuDNCnS", "last_name": "Mazur", "first_name": "Andrzej"}', '2025-01-07 18:17:26.490408'),(21, 'users', '{"id": 52, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$xzQOz/b1GEVF7Xq0JYVNWel7kk/Ng0TAAu1alD6hn9mUWuc3Pq7Cy", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-07 18:17:26.490408'),(22, 'users', '{"id": 55, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$mf7hf9XLQJISUDYxXydTv.hpcoMJznnv/T7LZ0VFsBk0IvbvTso4q", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-09 18:55:20.310685'),(23, 'users', '{"id": 56, "email": "andrzej@onet.pl", "password": "$2y$10$tMjI64US16pt5.CuW8fGQONoMtxfIPaRakcnKCkvbcnQuTEcKBmT2", "last_name": "Mazur", "first_name": "Andrzej"}', '2025-01-09 18:55:20.310685'),(24, 'users', '{"id": 57, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$VhRcb2UAJN3D48DIEl5j3.BId27bKVrObu1byoUubuxiBffzHtth2", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-12 12:07:45.545694'),(25, 'users', '{"id": 58, "email": "andrzej@onet.pl", "password": "$2y$10$D6rFeOrMWlyXsiEjOQGp5OgAxkKu11DgOpK6H6sGXNBtBLoC8ScJa", "last_name": "Mazur", "first_name": "Andrzej"}', '2025-01-12 12:07:45.545694'),(26, 'users', '{"id": 60, "email": "andrzej@onet.pl", "password": "$2y$10$rBb5TjLOrsiQKk7U4i1AdebOeUEO71nj3svvMIRkpx62CdroegG5G", "last_name": "Mazur", "first_name": "Andrzej"}', '2025-01-12 18:40:31.90632'),(27, 'users', '{"id": 59, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$guDkk1Mz01G8UqgImU2UaunLpPZL8mpJEGCMYOLg5ixLrQwEkVWJK", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-12 18:40:33.609819'),(28, 'users', '{"id": 61, "email": "patrix.p100@gmail.com", "password": "$2y$10$.5ulY.hCM7y4kmVz8eWUA.tXjxqkH4gL3hlkmcGh0TPzIxDflOWJe", "last_name": "Przybycinski", "first_name": "Patryk"}', '2025-01-13 14:16:49.018351'),(29, 'users', '{"id": 62, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$s/92iet9MV9iHDIJ8QEM9e8PCm9LV3GzIt6n6njrmyGsxQP592fTu", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-13 14:16:49.018351'),(30, 'users', '{"id": 63, "email": "andrzej@onet.pl", "password": "$2y$10$M3wFXWEDNgRalrj8Q5oB4Oun4EsLMTvNrVdSD3WJVceOMsNyf8TKq", "last_name": "Mazur", "first_name": "Andrzej"}', '2025-01-13 14:16:49.018351'),(31, 'users', '{"id": 64, "email": "kacper@onet.pl", "password": "$2y$10$y26f53XdKZKOVDxC.u/i8eHh0rKksLNWYqb2oGrMSr91uWjGsgLgm", "last_name": "Mrowiec", "first_name": "Kacper"}', '2025-01-13 14:16:49.018351'),(32, 'users', '{"id": 65, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$hylyqyPp6Jmx1zBnzh/cwey8OIRigy8//A2MYTa2IKOTGCd/X8.3y", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-13 14:17:36.320444'),(33, 'users', '{"id": 66, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$Icu/fco01jqEDLnU31GUxueAynJXLXma0S6ap6GRFfTCUd2IVT29a", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-13 14:24:19.023084'),(34, 'users', '{"id": 67, "email": "kacper@onet.pl", "password": "$2y$10$230QzSt8WgOC74V06LkIUu1oq/xdM2dmycjjxmm8lvj3dvatPdeAm", "last_name": "Mrowiec", "first_name": "Kacper"}', '2025-01-13 14:24:19.023084'),(35, 'users', '{"id": 68, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$x6hvD8HhPSM3.s2S.qzzz.CQoBJ.HfPU0Wre6dFcNLaAaydrc66Ce", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-13 14:26:11.971834'),(36, 'users', '{"id": 69, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$lkMj1ZKIIjx0WXG73z54aOPon35OVe7e5ZCkOQxV2onia6QAIARZu", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-13 14:26:55.167207'),(37, 'users', '{"id": 70, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$IQ7nAGixeejBJ.iQcCG8yuFKCjURHjH1tZwv/9m10kEVWa62fm7Ye", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-13 14:32:41.300835'),(38, 'users', '{"id": 71, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$TZR8HpvKN1iq8pV0LM35W.QlNm8tprEtx8b9fVhnXgqGBuAaSicxa", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-13 14:40:22.456376'),(39, 'users', '{"id": 72, "email": "kacper@onet.pl", "password": "$2y$10$oJ8t/FpUSdiH7J1Yb0Al.u.Go5ytvEziFrT5..i8K69GEbSx83Q8u", "last_name": "Mrowiec", "first_name": "Kacper"}', '2025-01-13 14:40:22.456376'),(40, 'users', '{"id": 74, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$fnQ0Gdg19jdwKgqOJfjKj.7sGNDWld.xcgupe.k9908WSWbA.ocAW", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-14 12:59:00.649967'),(41, 'users', '{"id": 75, "email": "kacper@onet.pl", "password": "$2y$10$Xcakk0y1vqLSQMFjjYoQ2uCfc8tsDJF7KHnHcwp6bH76Nl5Fd98Oy", "last_name": "Mrowiec", "first_name": "Kacper"}', '2025-01-14 12:59:00.649967'),(42, 'users', '{"id": 76, "email": "andrzej@onet.pl", "password": "$2y$10$5nQStDjXhTZYgOrnjuPeGOJ0/1QwsqFzhNgGfzrtYprFASgTGeFAW", "last_name": "Mazur", "first_name": "Andrzej"}', '2025-01-14 12:59:00.649967'),(43, 'users', '{"id": 77, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$r6l2WqHVyrKGTZovgz7lHef8X7xJCmUwJbmdcisHIoE1o3znPSBC.", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-14 15:39:59.265799'),(44, 'users', '{"id": 80, "email": "mazurkrzysztof377@gmail.com", "password": "$2y$10$fBaRRtKUyxegiA4pP8/NUO92JwIstbakVRfy3PaRift08wrXMe3yW", "last_name": "Mazur", "first_name": "Krzysztof"}', '2025-01-15 13:47:29.096331'),(45, 'users', '{"id": 78, "email": "andrzej@onet.pl", "password": "$2y$10$3bfXq1IxM/WE6SGrk8dOwONgjtKWx.Q0p7YYHIczJk8.kaVARc/VK", "last_name": "Mazur", "first_name": "Andrzej"}', '2025-01-15 13:47:44.043726'),(46, 'users', '{"id": 79, "email": "kacper@onet.pl", "password": "$2y$10$jadR822rg8N5wIppqF0nGe6nIyV3n6A07KFvmAcZW2.Qz8E/jaGxW", "last_name": "Mrowiec", "first_name": "Kacper"}', '2025-01-15 13:47:44.896734')
;
COMMIT;
BEGIN;
LOCK TABLE "public"."roles" IN SHARE MODE;
DELETE FROM "public"."roles";
INSERT INTO "public"."roles" ("id","role_name") VALUES (1, 'user'),(2, 'admin')
;
COMMIT;
BEGIN;
LOCK TABLE "public"."user_details" IN SHARE MODE;
DELETE FROM "public"."user_details";
INSERT INTO "public"."user_details" ("id","user_id","gender","height","weight","age","activity_level_id","daily_calories","consumed_calories","daily_protein","daily_fat","daily_carbohydrates","consumed_protein","consumed_fat","consumed_carbohydrates") VALUES (95, 81, 'male', 190, 80, 21, 1, 2344, 1799, 160, 65, 279, 82, 105, 125)
;
COMMIT;
BEGIN;
LOCK TABLE "public"."user_roles" IN SHARE MODE;
DELETE FROM "public"."user_roles";
INSERT INTO "public"."user_roles" ("user_id","role_id") VALUES (81, 2)
;
COMMIT;
BEGIN;
LOCK TABLE "public"."users" IN SHARE MODE;
DELETE FROM "public"."users";
INSERT INTO "public"."users" ("id","first_name","last_name","email","password") VALUES (81, 'Krzysztof', 'Mazur', 'mazurkrzysztof377@gmail.com', '$2y$10$TYSgpy9QBxXVGRGfzkvx/OqCXbyNpLdKbDrAZuQ2Z1YcNfIx.wo0e')
;
COMMIT;
ALTER TABLE "activity_levels" ADD CONSTRAINT "activity_levels_pkey" PRIMARY KEY ("id");
ALTER TABLE "deleted_records" ADD CONSTRAINT "deleted_records_pkey" PRIMARY KEY ("id");
ALTER TABLE "roles" ADD CONSTRAINT "roles_pkey" PRIMARY KEY ("id");
ALTER TABLE "user_details" ADD CONSTRAINT "user_details_pkey" PRIMARY KEY ("id");
CREATE UNIQUE INDEX "user_id_unique_idx" ON "user_details" USING btree (
  "user_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);
ALTER TABLE "user_roles" ADD CONSTRAINT "user_roles_pkey" PRIMARY KEY ("user_id", "role_id");
ALTER TABLE "users" ADD CONSTRAINT "users_pkey" PRIMARY KEY ("id");
ALTER TABLE "roles" ADD CONSTRAINT "roles_role_name_key" UNIQUE ("role_name");
ALTER TABLE "user_details" ADD CONSTRAINT "user_details_user_id_key" UNIQUE ("user_id");
ALTER TABLE "user_details" ADD CONSTRAINT "user_details_user_id_key1" UNIQUE ("user_id");
ALTER TABLE "user_details" ADD CONSTRAINT "unique_user_id" UNIQUE ("user_id");
ALTER TABLE "user_details" ADD CONSTRAINT "user_details_activity_level_id_fkey" FOREIGN KEY ("activity_level_id") REFERENCES "public"."activity_levels" ("id") ON DELETE SET NULL ON UPDATE NO ACTION;
ALTER TABLE "user_details" ADD CONSTRAINT "user_details_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users" ("id") ON DELETE CASCADE ON UPDATE NO ACTION;
CREATE TRIGGER "trigger_calculate_daily_calories_after_update" BEFORE INSERT OR UPDATE ON "user_details"
FOR EACH ROW
EXECUTE PROCEDURE "public"."trigger_calculate_daily_calories_after_update"();
ALTER TABLE "user_roles" ADD CONSTRAINT "user_roles_role_id_fkey" FOREIGN KEY ("role_id") REFERENCES "public"."roles" ("id") ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE "user_roles" ADD CONSTRAINT "user_roles_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users" ("id") ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE "users" ADD CONSTRAINT "users_email_key" UNIQUE ("email");
CREATE TRIGGER "trigger_log_deletes" AFTER DELETE ON "users"
FOR EACH ROW
EXECUTE PROCEDURE "public"."log_deleted_records"();
ALTER SEQUENCE "activity_levels_id_seq"
OWNED BY "activity_levels"."id";
SELECT setval('"activity_levels_id_seq"', 5, true);
ALTER SEQUENCE "activity_levels_id_seq" OWNER TO "docker";
ALTER SEQUENCE "deleted_records_id_seq"
OWNED BY "deleted_records"."id";
SELECT setval('"deleted_records_id_seq"', 46, true);
ALTER SEQUENCE "deleted_records_id_seq" OWNER TO "docker";
ALTER SEQUENCE "roles_id_seq"
OWNED BY "roles"."id";
SELECT setval('"roles_id_seq"', 2, true);
ALTER SEQUENCE "roles_id_seq" OWNER TO "docker";
ALTER SEQUENCE "user_details_id_seq"
OWNED BY "user_details"."id";
SELECT setval('"user_details_id_seq"', 95, true);
ALTER SEQUENCE "user_details_id_seq" OWNER TO "docker";
ALTER SEQUENCE "users_id_seq"
OWNED BY "users"."id";
SELECT setval('"users_id_seq"', 81, true);
ALTER SEQUENCE "users_id_seq" OWNER TO "docker";
