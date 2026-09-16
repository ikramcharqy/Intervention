part of 'security_settings_bloc.dart';

sealed class SecuritySettingsEvent extends Equatable {
  const SecuritySettingsEvent();

  @override
  List<Object?> get props => [];
}

class SecuritySettingsRequested extends SecuritySettingsEvent {
  const SecuritySettingsRequested();
}

class PasswordChangeSubmitted extends SecuritySettingsEvent {
  final String currentPassword;
  final String newPassword;
  final String confirmation;
  const PasswordChangeSubmitted({required this.currentPassword, required this.newPassword, required this.confirmation});

  @override
  List<Object?> get props => [currentPassword, newPassword, confirmation];
}

class NotificationPreferenceToggled extends SecuritySettingsEvent {
  final bool value;
  const NotificationPreferenceToggled(this.value);

  @override
  List<Object?> get props => [value];
}

class SessionRevokeRequested extends SecuritySettingsEvent {
  final int tokenId;
  const SessionRevokeRequested(this.tokenId);

  @override
  List<Object?> get props => [tokenId];
}
