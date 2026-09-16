import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/features/profile/domain/entities/security_entities.dart';
import 'package:mobile_technicien/features/profile/domain/usecases/profile_usecases.dart';

part 'security_settings_event.dart';
part 'security_settings_state.dart';

/// Regroupe les 3 volets de l'écran Paramètres (mot de passe, sécurité,
/// préférences de notification) dans un seul bloc plutôt que 3 — tous
/// n'existent que sur cet écran, pas de raison de les découpler.
class SecuritySettingsBloc extends Bloc<SecuritySettingsEvent, SecuritySettingsState> {
  final GetProfileUsecase getProfileUsecase;
  final UpdatePasswordUsecase updatePasswordUsecase;
  final UpdateNotificationPreferencesUsecase updateNotificationPreferencesUsecase;
  final GetLoginHistoryUsecase getLoginHistoryUsecase;
  final GetSessionsUsecase getSessionsUsecase;
  final RevokeSessionUsecase revokeSessionUsecase;

  SecuritySettingsBloc({
    required this.getProfileUsecase,
    required this.updatePasswordUsecase,
    required this.updateNotificationPreferencesUsecase,
    required this.getLoginHistoryUsecase,
    required this.getSessionsUsecase,
    required this.revokeSessionUsecase,
  }) : super(const SecuritySettingsLoading()) {
    on<SecuritySettingsRequested>(_onRequested);
    on<PasswordChangeSubmitted>(_onPasswordChangeSubmitted);
    on<NotificationPreferenceToggled>(_onNotificationPreferenceToggled);
    on<SessionRevokeRequested>(_onSessionRevokeRequested);
  }

  Future<void> _onRequested(SecuritySettingsRequested event, Emitter<SecuritySettingsState> emit) async {
    emit(const SecuritySettingsLoading());

    final profileResult = await getProfileUsecase();
    final sessionsResult = await getSessionsUsecase();
    final historyResult = await getLoginHistoryUsecase();

    await profileResult.when(
      success: (profile) async {
        emit(SecuritySettingsLoaded(
          sessions: sessionsResult.when(success: (s) => s, failure: (_) => const []),
          loginHistory: historyResult.when(success: (h) => h, failure: (_) => const []),
          notificationInterventionUpdates: profile.notificationInterventionUpdates,
        ));
      },
      failure: (f) async => emit(SecuritySettingsError(f.message)),
    );
  }

  Future<void> _onPasswordChangeSubmitted(PasswordChangeSubmitted event, Emitter<SecuritySettingsState> emit) async {
    final current = state;
    if (current is! SecuritySettingsLoaded) return;

    emit(current.copyWith(passwordChangeStatus: PasswordChangeStatus.submitting, passwordChangeError: null));
    final result = await updatePasswordUsecase(
      currentPassword: event.currentPassword,
      newPassword: event.newPassword,
      confirmation: event.confirmation,
    );
    result.when(
      success: (_) => emit(current.copyWith(passwordChangeStatus: PasswordChangeStatus.success, passwordChangeError: null)),
      failure: (f) => emit(current.copyWith(passwordChangeStatus: PasswordChangeStatus.error, passwordChangeError: f.message)),
    );
  }

  Future<void> _onNotificationPreferenceToggled(NotificationPreferenceToggled event, Emitter<SecuritySettingsState> emit) async {
    final current = state;
    if (current is! SecuritySettingsLoaded) return;

    // Optimiste — le toggle doit répondre immédiatement au tap.
    emit(current.copyWith(notificationInterventionUpdates: event.value));
    final result = await updateNotificationPreferencesUsecase(interventionUpdates: event.value);
    result.when(
      success: (_) {},
      // Échec : revient à l'état précédent plutôt que de laisser affiché un
      // état non réellement enregistré côté serveur.
      failure: (_) => emit(current.copyWith(notificationInterventionUpdates: !event.value)),
    );
  }

  Future<void> _onSessionRevokeRequested(SessionRevokeRequested event, Emitter<SecuritySettingsState> emit) async {
    final current = state;
    if (current is! SecuritySettingsLoaded) return;

    emit(current.copyWith(revokingSessionId: event.tokenId));
    final result = await revokeSessionUsecase(event.tokenId);
    result.when(
      success: (_) => emit(current.copyWith(
        sessions: current.sessions.where((s) => s.id != event.tokenId).toList(),
        clearRevoking: true,
      )),
      failure: (_) => emit(current.copyWith(clearRevoking: true)),
    );
  }
}
