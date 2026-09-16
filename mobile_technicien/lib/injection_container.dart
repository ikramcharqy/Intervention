import 'package:get_it/get_it.dart';
import 'package:mobile_technicien/core/network/api_client.dart';
import 'package:mobile_technicien/core/services/biometric_lock_service.dart';
import 'package:mobile_technicien/core/services/presence_service.dart';
import 'package:mobile_technicien/core/storage/secure_storage_service.dart';
import 'package:mobile_technicien/features/chantier/data/datasources/chantier_remote_datasource.dart';
import 'package:mobile_technicien/features/chantier/data/repositories/chantier_repository_impl.dart';
import 'package:mobile_technicien/features/chantier/domain/repositories/chantier_repository.dart';
import 'package:mobile_technicien/features/chantier/domain/usecases/get_chantier_detail_usecase.dart';
import 'package:mobile_technicien/features/chantier/presentation/bloc/chantier_detail_bloc.dart';
import 'package:mobile_technicien/features/auth/data/datasources/auth_remote_datasource.dart';
import 'package:mobile_technicien/features/auth/data/repositories/auth_repository_impl.dart';
import 'package:mobile_technicien/features/auth/domain/repositories/auth_repository.dart';
import 'package:mobile_technicien/features/auth/domain/usecases/get_current_user_usecase.dart';
import 'package:mobile_technicien/features/auth/domain/usecases/login_usecase.dart';
import 'package:mobile_technicien/features/auth/domain/usecases/logout_usecase.dart';
import 'package:mobile_technicien/features/auth/presentation/bloc/auth_bloc.dart';
import 'package:mobile_technicien/features/home/data/datasources/home_remote_datasource.dart';
import 'package:mobile_technicien/features/home/data/repositories/home_repository_impl.dart';
import 'package:mobile_technicien/features/home/domain/repositories/home_repository.dart';
import 'package:mobile_technicien/features/home/domain/usecases/get_home_summary_usecase.dart';
import 'package:mobile_technicien/features/home/domain/usecases/start_mission_usecase.dart';
import 'package:mobile_technicien/features/home/presentation/bloc/home_bloc.dart';
import 'package:mobile_technicien/features/formulaire/data/datasources/formulaire_remote_datasource.dart';
import 'package:mobile_technicien/features/formulaire/data/local/formulaire_draft_service.dart';
import 'package:mobile_technicien/features/formulaire/data/repositories/formulaire_repository_impl.dart';
import 'package:mobile_technicien/features/formulaire/domain/repositories/formulaire_repository.dart';
import 'package:mobile_technicien/features/formulaire/domain/usecases/finish_intervention_usecase.dart';
import 'package:mobile_technicien/features/formulaire/domain/usecases/get_formulaire_usecase.dart';
import 'package:mobile_technicien/features/formulaire/domain/usecases/submit_formulaire_usecase.dart';
import 'package:mobile_technicien/features/formulaire/presentation/bloc/dynamic_form_bloc.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/missions/data/repositories/missions_repository_impl.dart';
import 'package:mobile_technicien/features/missions/domain/repositories/missions_repository.dart';
import 'package:mobile_technicien/features/missions/domain/usecases/accept_mission_usecase.dart';
import 'package:mobile_technicien/features/missions/domain/usecases/get_mission_detail_usecase.dart';
import 'package:mobile_technicien/features/missions/domain/usecases/get_missions_usecase.dart';
import 'package:mobile_technicien/features/missions/domain/usecases/refuse_mission_usecase.dart';
import 'package:mobile_technicien/features/missions/domain/usecases/start_mission_usecase.dart' as missions_usecases;
import 'package:mobile_technicien/features/missions/presentation/bloc/mission_detail_bloc.dart';
import 'package:mobile_technicien/features/missions/presentation/bloc/missions_bloc.dart';
import 'package:mobile_technicien/features/rapport/data/datasources/rapport_remote_datasource.dart';
import 'package:mobile_technicien/features/rapport/data/repositories/rapport_repository_impl.dart';
import 'package:mobile_technicien/features/rapport/domain/repositories/rapport_repository.dart';
import 'package:mobile_technicien/features/rapport/domain/usecases/get_rapport_by_intervention_usecase.dart';
import 'package:mobile_technicien/features/rapport/domain/usecases/get_rapport_pdf_bytes_usecase.dart';
import 'package:mobile_technicien/features/rapport/presentation/bloc/rapport_detail_bloc.dart';
import 'package:mobile_technicien/features/notifications/data/datasources/notifications_remote_datasource.dart';
import 'package:mobile_technicien/features/notifications/data/repositories/notifications_repository_impl.dart';
import 'package:mobile_technicien/features/notifications/domain/repositories/notifications_repository.dart';
import 'package:mobile_technicien/features/notifications/domain/usecases/get_notifications_usecase.dart';
import 'package:mobile_technicien/features/notifications/domain/usecases/mark_all_notifications_read_usecase.dart';
import 'package:mobile_technicien/features/notifications/domain/usecases/mark_notification_read_usecase.dart';
import 'package:mobile_technicien/features/notifications/presentation/bloc/notifications_bloc.dart';
import 'package:mobile_technicien/features/profile/data/datasources/profile_remote_datasource.dart';
import 'package:mobile_technicien/features/profile/data/repositories/profile_repository_impl.dart';
import 'package:mobile_technicien/features/profile/domain/repositories/profile_repository.dart';
import 'package:mobile_technicien/features/profile/domain/usecases/profile_usecases.dart';
import 'package:mobile_technicien/features/profile/presentation/bloc/profile_bloc.dart';
import 'package:mobile_technicien/features/profile/presentation/bloc/security_settings_bloc.dart';

final sl = GetIt.instance;

/// Câblage Clean Architecture : core (singletons) → data (datasources/repos)
/// → domain (usecases) → presentation (BLoC, factory — un par écran ouvert).
Future<void> initDependencies() async {
  // Core
  sl.registerLazySingleton(() => SecureStorageService());
  sl.registerLazySingleton(() => ApiClient(storage: sl()));
  sl.registerLazySingleton(() => BiometricLockService());
  sl.registerLazySingleton(() => PresenceService(sl()));

  // Auth
  sl.registerLazySingleton(() => AuthRemoteDatasource(sl()));
  sl.registerLazySingleton<AuthRepository>(() => AuthRepositoryImpl(remote: sl(), storage: sl()));
  sl.registerLazySingleton(() => LoginUsecase(sl()));
  sl.registerLazySingleton(() => GetCurrentUserUsecase(sl()));
  sl.registerLazySingleton(() => LogoutUsecase(sl()));
  sl.registerFactory(() => AuthBloc(
        loginUsecase: sl(),
        getCurrentUserUsecase: sl(),
        logoutUsecase: sl(),
        repository: sl(),
      ));

  // Home
  sl.registerLazySingleton(() => HomeRemoteDatasource(sl()));
  sl.registerLazySingleton<HomeRepository>(() => HomeRepositoryImpl(sl()));
  sl.registerLazySingleton(() => GetHomeSummaryUsecase(sl()));
  sl.registerLazySingleton(() => StartMissionUsecase(sl()));
  sl.registerFactory(() => HomeBloc(getHomeSummaryUsecase: sl()));

  // Missions — réutilise HomeRemoteDatasource (même GET /api/interventions
  // que l'accueil, pas un second endpoint) pour garantir que les compteurs de
  // l'accueil et la liste Missions ne divergent jamais.
  sl.registerLazySingleton<MissionsRepository>(() => MissionsRepositoryImpl(sl()));
  sl.registerLazySingleton(() => GetMissionsUsecase(sl()));
  sl.registerFactory(() => MissionsBloc(getMissionsUsecase: sl()));

  // Détail mission (Étape 2) — un MissionDetailBloc par écran ouvert, seedé
  // avec la Mission déjà connue côté liste pour un affichage immédiat, avant
  // même le premier rafraîchissement réseau.
  sl.registerLazySingleton(() => GetMissionDetailUsecase(sl()));
  sl.registerLazySingleton(() => AcceptMissionUsecase(sl()));
  sl.registerLazySingleton(() => RefuseMissionUsecase(sl()));
  sl.registerLazySingleton(() => missions_usecases.StartMissionUsecase(sl()));

  // Formulaire dynamique (Étape 3-4) — endpoint dédié (FormulaireController),
  // distinct de HomeRemoteDatasource/MissionsRepository. Enregistré avant
  // MissionDetailBloc : celui-ci consomme FinishInterventionUsecase pour le
  // bouton "Soumettre le rapport" (POST /interventions/{id}/finish).
  sl.registerLazySingleton(() => FormulaireDraftService());
  sl.registerLazySingleton(() => FormulaireRemoteDatasource(sl()));
  sl.registerLazySingleton<FormulaireRepository>(() => FormulaireRepositoryImpl(sl()));
  sl.registerLazySingleton(() => GetFormulaireUsecase(sl()));
  sl.registerLazySingleton(() => SubmitFormulaireUsecase(sl()));
  sl.registerLazySingleton(() => FinishInterventionUsecase(sl()));

  sl.registerFactoryParam<MissionDetailBloc, Mission, void>(
    (mission, _) => MissionDetailBloc(
      initialMission: mission,
      getMissionDetailUsecase: sl(),
      acceptMissionUsecase: sl(),
      refuseMissionUsecase: sl(),
      startMissionUsecase: sl(),
      finishInterventionUsecase: sl(),
    ),
  );

  sl.registerFactoryParam<DynamicFormBloc, int, void>(
    (interventionId, _) => DynamicFormBloc(
      interventionId: interventionId,
      getFormulaireUsecase: sl(),
      submitFormulaireUsecase: sl(),
      draftService: sl(),
    ),
  );

  // Fiche chantier (écran de détail mission enrichi) — GET /api/chantiers/{id},
  // endpoint technicien dédié distinct de HomeRemoteDatasource/MissionsRepository.
  sl.registerLazySingleton(() => ChantierRemoteDatasource(sl()));
  sl.registerLazySingleton<ChantierRepository>(() => ChantierRepositoryImpl(sl()));
  sl.registerLazySingleton(() => GetChantierDetailUsecase(sl()));
  sl.registerFactoryParam<ChantierDetailBloc, int, void>(
    (chantierId, _) => ChantierDetailBloc(chantierId: chantierId, getChantierDetailUsecase: sl()),
  );

  // Rapport (statut Terminée) — GET /api/interventions/{id}/rapport et
  // GET /api/rapports/{id}/pdf, même structure déjà normalisée côté web.
  sl.registerLazySingleton(() => RapportRemoteDatasource(sl()));
  sl.registerLazySingleton<RapportRepository>(() => RapportRepositoryImpl(sl()));
  sl.registerLazySingleton(() => GetRapportByInterventionUsecase(sl()));
  sl.registerLazySingleton(() => GetRapportPdfBytesUsecase(sl()));
  sl.registerFactoryParam<RapportDetailBloc, int, void>(
    (interventionId, _) => RapportDetailBloc(
      interventionId: interventionId,
      getRapportByInterventionUsecase: sl(),
      getRapportPdfBytesUsecase: sl(),
    ),
  );

  // Notifications — GET /api/notifications (Api\NotificationController),
  // logique de regroupement par période et de lu/non-lu réutilisée depuis le
  // portail Client, adaptée en Dart (pas d'endpoint de regroupement serveur).
  sl.registerLazySingleton(() => NotificationsRemoteDatasource(sl()));
  sl.registerLazySingleton<NotificationsRepository>(() => NotificationsRepositoryImpl(sl()));
  sl.registerLazySingleton(() => GetNotificationsUsecase(sl()));
  sl.registerLazySingleton(() => MarkNotificationReadUsecase(sl()));
  sl.registerLazySingleton(() => MarkAllNotificationsReadUsecase(sl()));
  sl.registerFactory(() => NotificationsBloc(
        getNotificationsUsecase: sl(),
        markNotificationReadUsecase: sl(),
        markAllNotificationsReadUsecase: sl(),
      ));

  // Profil / Paramètres — GET /api/profile (statistiques déjà calculées côté
  // Api\ProfileController::show(), pas de recalcul côté app) + volet Sécurité
  // (ApiSessionService : historique de connexion, sessions Sanctum actives).
  sl.registerLazySingleton(() => ProfileRemoteDatasource(sl()));
  sl.registerLazySingleton<ProfileRepository>(() => ProfileRepositoryImpl(sl()));
  sl.registerLazySingleton(() => GetProfileUsecase(sl()));
  sl.registerLazySingleton(() => UpdatePasswordUsecase(sl()));
  sl.registerLazySingleton(() => UpdateNotificationPreferencesUsecase(sl()));
  sl.registerLazySingleton(() => GetLoginHistoryUsecase(sl()));
  sl.registerLazySingleton(() => GetSessionsUsecase(sl()));
  sl.registerLazySingleton(() => RevokeSessionUsecase(sl()));
  sl.registerFactory(() => ProfileBloc(getProfileUsecase: sl()));
  sl.registerFactory(() => SecuritySettingsBloc(
        getProfileUsecase: sl(),
        updatePasswordUsecase: sl(),
        updateNotificationPreferencesUsecase: sl(),
        getLoginHistoryUsecase: sl(),
        getSessionsUsecase: sl(),
        revokeSessionUsecase: sl(),
      ));
}
