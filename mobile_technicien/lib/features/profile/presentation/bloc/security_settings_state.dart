part of 'security_settings_bloc.dart';

enum PasswordChangeStatus { idle, submitting, success, error }

sealed class SecuritySettingsState extends Equatable {
  const SecuritySettingsState();

  @override
  List<Object?> get props => [];
}

class SecuritySettingsLoading extends SecuritySettingsState {
  const SecuritySettingsLoading();
}

class SecuritySettingsError extends SecuritySettingsState {
  final String message;
  const SecuritySettingsError(this.message);

  @override
  List<Object?> get props => [message];
}

class SecuritySettingsLoaded extends SecuritySettingsState {
  final List<ActiveSession> sessions;
  final List<LoginHistoryEntry> loginHistory;
  final bool notificationInterventionUpdates;
  final PasswordChangeStatus passwordChangeStatus;
  final String? passwordChangeError;
  final int? revokingSessionId;

  const SecuritySettingsLoaded({
    required this.sessions,
    required this.loginHistory,
    required this.notificationInterventionUpdates,
    this.passwordChangeStatus = PasswordChangeStatus.idle,
    this.passwordChangeError,
    this.revokingSessionId,
  });

  SecuritySettingsLoaded copyWith({
    List<ActiveSession>? sessions,
    List<LoginHistoryEntry>? loginHistory,
    bool? notificationInterventionUpdates,
    PasswordChangeStatus? passwordChangeStatus,
    String? passwordChangeError,
    int? revokingSessionId,
    bool clearRevoking = false,
  }) {
    return SecuritySettingsLoaded(
      sessions: sessions ?? this.sessions,
      loginHistory: loginHistory ?? this.loginHistory,
      notificationInterventionUpdates: notificationInterventionUpdates ?? this.notificationInterventionUpdates,
      passwordChangeStatus: passwordChangeStatus ?? this.passwordChangeStatus,
      passwordChangeError: passwordChangeError,
      revokingSessionId: clearRevoking ? null : (revokingSessionId ?? this.revokingSessionId),
    );
  }

  @override
  List<Object?> get props => [
        sessions,
        loginHistory,
        notificationInterventionUpdates,
        passwordChangeStatus,
        passwordChangeError,
        revokingSessionId,
      ];
}
